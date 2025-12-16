<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesTransaction;
use App\Models\TransactionDetail;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use PDF;

class SalesTransactionController extends Controller
{
    public function index()
    {
        $transactions = SalesTransaction::with(['customer', 'user', 'details.product'])
            ->orderBy('transaction_ID', 'desc')
            ->get();
        
        return view('sales.index', compact('transactions'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::all();
        $users = User::all();
        return view('sales.create', compact('customers', 'products', 'users'));
    }

    /**
     * Show the form for creating a walk-in sale (no customer).
     */
    public function createWalkIn()
    {
        $products = Product::where(function($query) {
            $query->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', Carbon::today());
        })->where('Quantity_in_Stock', '>', 0)->get();
        
        return view('sales.walkin_create', compact('products'));
    }

    /**
     * Store a walk-in sale (no customer).
     */
    public function storeWalkIn(Request $request)
    {
        \Log::info('Walk-in sale store method called');
        \Log::info('Request data:', $request->all());
        
        $request->validate([
            'User_ID' => 'required|exists:users,User_ID',
            'payment_method' => 'required|in:Cash,GCash',
            'products' => 'required|array|min:1',
            'products.*.Product_ID' => 'required|exists:products,Product_ID',
            'products.*.Quantity' => 'required|numeric|min:1',
            'products.*.Kilo' => 'required|numeric|min:0.1',
            'products.*.Price' => 'required|numeric|min:0',
        ]);

        $total = 0;
        foreach ($request->products as $p) {
            $kilo = $p['Kilo'];
            $total += $kilo * $p['Price'];
        }

        \DB::beginTransaction();
        
        try {
            $receiptNumber = 'WALK-' . now()->format('YmdHis') . '-' . rand(1000, 9999);
            
            // Create transaction WITHOUT Customer_ID (it will be NULL)
            $transaction = new SalesTransaction();
            $transaction->User_ID = $request->User_ID;
            $transaction->transaction_date = now();
            $transaction->total_amount = $total;
            $transaction->payment_method = $request->payment_method;
            $transaction->receipt_number = $receiptNumber;
            $transaction->status = 'pending';
            // Customer_ID is not set, so it will be NULL
            $transaction->save();

            \Log::info('Walk-in transaction created with ID: ' . $transaction->transaction_ID);

            foreach ($request->products as $p) {
                $kilo = $p['Kilo'];
                $quantity = $p['Quantity'];
                
                $product = Product::find($p['Product_ID']);
                if (!$product) {
                    throw new \Exception("Product not found");
                }
                
                if ($product->Quantity_in_Stock < $quantity) {
                    throw new \Exception("Insufficient stock for {$product->Product_Name}. Available: {$product->Quantity_in_Stock}, Requested: {$quantity}");
                }
                
                // Store both Quantity (for stock tracking) and Kilo (for pricing)
                TransactionDetail::create([
                    'transaction_ID' => $transaction->transaction_ID,
                    'Product_ID' => $p['Product_ID'],
                    'Quantity' => $quantity,
                    'Kilo' => $kilo,
                    'Price' => $p['Price'],
                    'unit_price' => $p['Price'],
                ]);

                // Deduct the quantity from stock
                $product->decrement('Quantity_in_Stock', $quantity);
            }

            \DB::commit();
            
            return redirect()->route('sales.index')->with('success', 'Walk-in sale recorded successfully. Receipt: ' . $receiptNumber);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Walk-in sale failed: ' . $e->getMessage());
            return back()->with('error', 'Transaction failed: ' . $e->getMessage())->withInput();
        }
    }

    public function store(Request $request)
    {
        // DEBUG: Log what we receive
        \Log::info('Store method called');
        \Log::info('Request data:', $request->all());
        
        // TEMPORARILY DISABLE VALIDATION FOR TESTING
        /*
        $request->validate([
            'Customer_ID' => 'nullable|exists:customers,Customer_ID',
            'User_ID' => 'required|exists:users,User_ID',
            'payment_method' => 'required|in:Cash,GCash',
            'products.*.Product_ID' => 'required|exists:products,Product_ID',
            'products.*.Quantity' => 'required|numeric|min:0.1',
            'products.*.Kilo' => 'required|numeric|min:0.1',
            'products.*.Price' => 'required|numeric|min:0',
        ]);
        */

        // Convert empty Customer_ID to null for walk-in customers
        $customerId = $request->input('Customer_ID') ?: null;

        $total = 0;
        foreach ($request->products as $p) {
            $kilo = $p['Kilo'];
            $total += $kilo * $p['Price'];
        }

        \DB::beginTransaction();
        
        try {
            $receiptNumber = 'RCPT-' . now()->format('YmdHis') . '-' . rand(1000, 9999);
            
            \Log::info('Creating transaction with:', [
                'Customer_ID' => $customerId,
                'User_ID' => $request->User_ID,
                'total_amount' => $total
            ]);
            
            $transaction = SalesTransaction::create([
                'Customer_ID' => $customerId,
                'User_ID' => $request->User_ID,
                'transaction_date' => now(),
                'total_amount' => $total,
                'payment_method' => $request->payment_method,
                'receipt_number' => $receiptNumber,
                'status' => 'pending',
            ]);

            \Log::info('Transaction created with ID: ' . $transaction->transaction_ID);

            foreach ($request->products as $p) {
                $kilo = $p['Kilo'];
                $quantity = $p['Quantity'];
                
                $product = Product::find($p['Product_ID']);
                if (!$product) {
                    throw new \Exception("Product not found");
                }
                
                if ($product->Quantity_in_Stock < $quantity) {
                    throw new \Exception("Insufficient stock for {$product->Product_Name}. Available: {$product->Quantity_in_Stock}, Requested: {$quantity}");
                }
                
                // Store both Quantity (for stock tracking) and Kilo (for pricing)
                TransactionDetail::create([
                    'transaction_ID' => $transaction->transaction_ID,
                    'Product_ID' => $p['Product_ID'],
                    'Quantity' => $quantity,  // Store the actual quantity deducted from stock
                    'Kilo' => $kilo,          // Store the kilo for pricing calculation
                    'Price' => $p['Price'],   // Store the price per kilo
                    'unit_price' => $p['Price'],
                ]);

                // Deduct the quantity from stock
                $product->decrement('Quantity_in_Stock', $quantity);
            }

            \DB::commit();
            
            // Debug: Log the transaction
            \Log::info('Transaction created:', [
                'transaction_ID' => $transaction->transaction_ID,
                'Customer_ID' => $customerId,
                'User_ID' => $request->User_ID,
                'total_amount' => $total
            ]);
            
            return redirect()->route('sales.index')->with('success', 'Transaction recorded successfully. ID: ' . $transaction->transaction_ID);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            
            return back()->with('error', 'Transaction failed: ' . $e->getMessage())->withInput();
        }
    }

    // FIXED: Load products with stock info for editing
    public function edit(SalesTransaction $sale)
    {
        $customers = Customer::all();
        $users = User::all();
        
        // Load products and add back the quantity that was deducted for this sale
        $products = Product::all()->map(function($product) use ($sale) {
            // Find if this product is in the current sale
            $detailInSale = $sale->details->where('Product_ID', $product->Product_ID)->first();
            
            if ($detailInSale) {
                // Add back the quantity that was deducted for this specific sale
                // We need to find the ACTUAL quantity deducted (stored separately from Kilo)
                // Since we don't have it stored, we'll restore based on the Quantity field
                $product->available_stock = $product->Quantity_in_Stock + $detailInSale->Quantity;
            } else {
                $product->available_stock = $product->Quantity_in_Stock;
            }
            
            return $product;
        });
        
        return view('sales.edit', compact('sale', 'customers', 'products', 'users'));
    }

    // FIXED: Properly handle stock restoration and deduction
    public function update(Request $request, SalesTransaction $sale)
    {
        $request->validate([
            'Customer_ID' => 'nullable|exists:customers,Customer_ID',
            'User_ID' => 'required|exists:users,User_ID',
            'payment_method' => 'required|in:Cash,GCash',
            'products.*.Product_ID' => 'required|exists:products,Product_ID',
            'products.*.Quantity' => 'required|numeric|min:0.1',
            'products.*.Kilo' => 'required|numeric|min:0.1',
            'products.*.Price' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,paid',
        ]);

        // Convert empty Customer_ID to null for walk-in customers
        $customerId = $request->input('Customer_ID') ?: null;

        \DB::beginTransaction();
        
        try {
            // CRITICAL FIX: Restore stock using the ACTUAL Quantity field (not Kilo)
            // We need to track original quantities deducted
            $originalQuantities = [];
            foreach ($sale->details as $oldDetail) {
                $originalQuantities[$oldDetail->Product_ID] = $oldDetail->Quantity;
                
                $product = Product::find($oldDetail->Product_ID);
                if ($product) {
                    // Restore using the KILO value that was stored (which represents actual quantity deducted)
                    $product->increment('Quantity_in_Stock', $oldDetail->Quantity);
                }
            }

            // Update basic transaction info
            $sale->update([
                'Customer_ID' => $customerId,
                'User_ID' => $request->User_ID,
                'payment_method' => $request->payment_method,
                'status' => $request->status ?? $sale->status,
            ]);

            // Delete old details
            $sale->details()->delete();
            
            // Calculate new total and create new details
            $total = 0;
            
            foreach ($request->products as $p) {
                $kilo = $p['Kilo'];
                $quantity = $p['Quantity'];
                $lineTotal = $kilo * $p['Price'];
                $total += $lineTotal;
                
                // Check stock availability
                $product = Product::find($p['Product_ID']);
                if (!$product) {
                    throw new \Exception("Product not found");
                }
                
                if ($product->Quantity_in_Stock < $quantity) {
                    throw new \Exception("Insufficient stock for {$product->Product_Name}. Available: {$product->Quantity_in_Stock}, Requested: {$quantity}");
                }
                
                // Store both Quantity (for stock tracking) and Kilo (for pricing)
                TransactionDetail::create([
                    'transaction_ID' => $sale->transaction_ID,
                    'Product_ID' => $p['Product_ID'],
                    'Quantity' => $quantity,  // Store the actual quantity deducted from stock
                    'Kilo' => $kilo,          // Store the kilo for pricing calculation
                    'Price' => $p['Price'],   // Store the price per kilo
                    'unit_price' => $p['Price'],
                ]);

                // Deduct the NEW quantity
                $product->decrement('Quantity_in_Stock', $quantity);
            }
            
            // Update total amount
            $sale->update(['total_amount' => $total]);
            
            \DB::commit();

            return redirect()->route('sales.index')->with('success', 'Transaction updated successfully.');
            
        } catch (\Exception $e) {
            \DB::rollBack();
            
            return back()->with('error', 'Update failed: ' . $e->getMessage())->withInput();
        }
    }

    public function markPaid(SalesTransaction $sale)
    {
        $sale->update(['status' => 'paid']);
        return redirect()->route('sales.printReceipt', $sale->transaction_ID);
    }

    public function printReceipt(SalesTransaction $sale)
    {
        $sale->load(['customer', 'user', 'details.product']);
        return view('sales.receipt', compact('sale'));
    }

    public function destroy(SalesTransaction $sale)
    {
        foreach ($sale->details as $detail) {
            $product = $detail->product;
            if ($product) {
                $product->increment('Quantity_in_Stock', $detail->Quantity);
            }
        }

        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Transaction deleted successfully.');
    }

    public function details(SalesTransaction $sale)
    {
        $sale->load(['customer', 'user', 'details.product']);
        
        $response = [
            'transaction_ID' => $sale->transaction_ID,
            'receipt_number' => $sale->receipt_number,
            'transaction_date' => $sale->transaction_date,
            'total_amount' => $sale->total_amount,
            'payment_method' => $sale->payment_method,
            'status' => $sale->status,
            'customer' => [
                'Customer_Name' => $sale->customer ? $sale->customer->Customer_Name : 'Walk-in Customer'
            ],
            'user' => [
                'fname' => $sale->user->fname ?? 'N/A',
                'lname' => $sale->user->lname ?? ''
            ],
            'details' => $sale->details->map(function($detail) {
                return [
                    'Quantity' => $detail->Quantity,
                    'unit_price' => $detail->unit_price,
                    'product' => [
                        'Product_Name' => $detail->product->Product_Name . 
                            ($detail->product->variety ? ' - ' . $detail->product->variety : '')
                    ]
                ];
            })
        ];
        
        return response()->json($response);
    }

    public function report(Request $request)
    {
        // FIXED: Remove whereHas('customer') to include walk-in customers
        $query = SalesTransaction::with(['customer', 'user', 'details.product'])
            ->whereHas('user'); // Only require user, not customer

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('customer_id')) {
            $query->where('Customer_ID', $request->customer_id);
        }

        $sales = $query->orderBy('transaction_date', 'DESC')->get();

        // FIXED: Remove whereHas('customer') from all analytics queries
        $totalSales = SalesTransaction::sum('total_amount');
        $todaySales = SalesTransaction::whereDate('transaction_date', today())->sum('total_amount');
        $weeklySales = SalesTransaction::whereBetween('transaction_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_amount');
        $monthlySales = SalesTransaction::whereMonth('transaction_date', now()->month)->whereYear('transaction_date', now()->year)->sum('total_amount');
        $yearlySales = SalesTransaction::whereYear('transaction_date', now()->year)->sum('total_amount');

        $analytics = [
            'total_transactions' => SalesTransaction::count(),
            'today_transactions' => SalesTransaction::whereDate('transaction_date', today())->count(),
            'pending_transactions' => SalesTransaction::where('status', 'pending')->count(),
            'paid_transactions' => SalesTransaction::where('status', 'paid')->count(),
            'cash_total' => SalesTransaction::where('payment_method', 'Cash')->sum('total_amount'),
            'gcash_total' => SalesTransaction::where('payment_method', 'GCash')->sum('total_amount'),
            'avg_transaction_value' => SalesTransaction::avg('total_amount'),
            'top_customer' => SalesTransaction::select('Customer_ID')
                ->selectRaw('SUM(total_amount) as total_spent')
                ->whereNotNull('Customer_ID') // Only get registered customers for "top customer"
                ->groupBy('Customer_ID')
                ->orderByDesc('total_spent')
                ->with('customer')
                ->first(),
            'last_month_sales' => SalesTransaction::whereMonth('transaction_date', now()->subMonth()->month)->whereYear('transaction_date', now()->subMonth()->year)->sum('total_amount'),
        ];

        if ($analytics['last_month_sales'] > 0) {
            $analytics['growth_percentage'] = (($monthlySales - $analytics['last_month_sales']) / $analytics['last_month_sales']) * 100;
        } else {
            $analytics['growth_percentage'] = $monthlySales > 0 ? 100 : 0;
        }

        $topProducts = TransactionDetail::select('Product_ID')
            ->selectRaw('SUM(Quantity) as total_quantity')
            ->selectRaw('SUM(Quantity * unit_price) as total_revenue')
            ->groupBy('Product_ID')
            ->orderByDesc('total_revenue')
            ->with('product')
            ->limit(5)
            ->get();
            
        $customers = Customer::orderBy('Customer_Name')->get();

        $salesTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $salesTrend[] = [
                'date' => $date->format('M d'),
                'amount' => SalesTransaction::whereDate('transaction_date', $date)->sum('total_amount')
            ];
        }

        return view('sales.report', compact('sales', 'totalSales', 'todaySales', 'weeklySales', 'monthlySales', 'yearlySales', 'analytics', 'topProducts', 'customers', 'salesTrend'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // FIXED: Remove whereHas('customer') to include walk-in customers
        $sales = SalesTransaction::with(['customer', 'user', 'details.product'])
            ->whereBetween('transaction_date', [$request->start_date, $request->end_date])
            ->orderBy('transaction_date', 'DESC')
            ->get();

        $summary = [
            'total_sales' => $sales->sum('total_amount'),
            'total_transactions' => $sales->count(),
            'paid_amount' => $sales->where('status', 'paid')->sum('total_amount'),
            'pending_amount' => $sales->where('status', 'pending')->sum('total_amount'),
            'cash_sales' => $sales->where('payment_method', 'Cash')->sum('total_amount'),
            'gcash_sales' => $sales->where('payment_method', 'GCash')->sum('total_amount'),
            'avg_transaction' => $sales->avg('total_amount'),
            'paid_count' => $sales->where('status', 'paid')->count(),
            'pending_count' => $sales->where('status', 'pending')->count(),
        ];

        $productSales = [];
        foreach ($sales as $sale) {
            foreach ($sale->details as $detail) {
                $productName = $detail->product->Product_Name ?? 'Unknown';
                if (!isset($productSales[$productName])) {
                    $productSales[$productName] = ['quantity' => 0, 'revenue' => 0];
                }
                $productSales[$productName]['quantity'] += $detail->Quantity;
                $productSales[$productName]['revenue'] += $detail->Quantity * $detail->unit_price;
            }
        }

        arsort($productSales);
        $topProducts = array_slice($productSales, 0, 5, true);

        $pdf = PDF::loadView('sales.report_pdf', compact('sales', 'summary', 'topProducts', 'request'));

        return $pdf->download('sales_report_' . $request->start_date . '_to_' . $request->end_date . '.pdf');
    }

    public function getSalesData(Request $request)
    {
        $period = $request->input('period', 'week');
        $data = [];

        // FIXED: Remove whereHas('customer') to include walk-in customers
        switch ($period) {
            case 'week':
                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $data[] = [
                        'label' => $date->format('D'), 
                        'value' => SalesTransaction::whereDate('transaction_date', $date)->sum('total_amount')
                    ];
                }
                break;
            case 'month':
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $data[] = [
                        'label' => $date->format('M d'), 
                        'value' => SalesTransaction::whereDate('transaction_date', $date)->sum('total_amount')
                    ];
                }
                break;
            case 'year':
                for ($i = 11; $i >= 0; $i--) {
                    $date = Carbon::now()->subMonths($i);
                    $data[] = [
                        'label' => $date->format('M'), 
                        'value' => SalesTransaction::whereMonth('transaction_date', $date->month)
                            ->whereYear('transaction_date', $date->year)
                            ->sum('total_amount')
                    ];
                }
                break;
        }

        return response()->json($data);
    }
}