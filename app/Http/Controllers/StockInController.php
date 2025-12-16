<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockIn;
use App\Models\Product;
use App\Models\SupplierTransaction;
use App\Models\StockEditHistory;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    public function index()
    {
        $stockIns = StockIn::with('product')->orderBy('date', 'desc')->get();
        $products = Product::with('supplier')->get();
        
        return view('stockins.index', compact('stockIns', 'products'));
    }

    public function create()
    {
        $products = Product::with('supplier')->get();
        
        // Get latest COMPLETED supplier transactions with remaining quantity
        $latestSupplierTransactions = [];
        
        foreach ($products as $product) {
            // Find the most recent completed/paid transaction
            $latest = SupplierTransaction::where('Product_ID', $product->Product_ID)
                ->whereIn('status', ['completed', 'paid'])
                ->latest('Supply_transac_ID')
                ->first();
            
            if ($latest) {
                $suppliedQty = (float)$latest->quantity_units;
                
                // Calculate how much has already been stocked in from THIS SPECIFIC transaction
                $alreadyStockedQty = StockIn::where('supplier_transaction_id', $latest->Supply_transac_ID)
                    ->sum('quantity');
                
                // Calculate remaining quantity available for stock-in
                $remainingQty = $suppliedQty - $alreadyStockedQty;
                
                // Only show if there's remaining quantity
                if ($remainingQty > 0) {
                    // Calculate price per unit from supplier transaction
                    $pricePerUnit = $suppliedQty > 0 ? ($latest->total_cost / $suppliedQty) : 0;
                    
                    $latestSupplierTransactions[$product->Product_ID] = [
                        'quantity' => $remainingQty,
                        'original_quantity' => $suppliedQty,
                        'already_stocked' => $alreadyStockedQty,
                        'price' => round($pricePerUnit, 2),
                        'date' => $latest->supply_date,
                        'transaction_id' => $latest->Supply_transac_ID,
                    ];
                }
            }
        }
        
        return view('stockins.create', compact('products', 'latestSupplierTransactions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Product_ID' => 'required|exists:products,Product_ID',
            'date' => 'required|date',
            'quantity' => 'required|numeric|min:0.01',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string',
            'expiry_date' => 'nullable|date',
            'critical_level' => 'required|integer|min:0',
            'supplier_transaction_id' => 'nullable|exists:supplier_transactions,Supply_transac_ID',
        ]);

        // SERVER-SIDE VALIDATION: Check remaining quantity if linked to supplier transaction
        if ($request->supplier_transaction_id) {
            $transaction = SupplierTransaction::findOrFail($request->supplier_transaction_id);
            
            $suppliedQty = (float)$transaction->quantity_units;
            
            // Calculate already stocked quantity from this specific transaction
            $alreadyStockedQty = StockIn::where('supplier_transaction_id', $request->supplier_transaction_id)
                ->sum('quantity');
            
            $remainingQty = $suppliedQty - $alreadyStockedQty;
            
            if ($request->quantity > $remainingQty) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'quantity' => 'Quantity (' . $request->quantity . ') exceeds remaining quantity (' . round($remainingQty, 2) . '). Already stocked: ' . round($alreadyStockedQty, 2) . ' out of ' . round($suppliedQty, 2) . ' supplied.'
                    ]);
            }
        }

        // Create the stock-in record with supplier transaction link
        StockIn::create($request->all());

        // Get product and store old values BEFORE making changes
        $product = Product::findOrFail($request->Product_ID);
        
        $oldQuantity = $product->Quantity_in_Stock ?? 0;
        $oldPrice = $product->unit_price ?? 0;
        $oldExpiryDate = $product->expiry_date;
        
        $today = now()->startOfDay();
        $isCurrentStockExpired = $product->expiry_date && $product->expiry_date < $today;
        
        if ($isCurrentStockExpired) {
            // **FIFO: Replace expired stock with new stock (don't add)**
            $product->Quantity_in_Stock = $request->quantity;
            
            // Update to new expiry date
            $product->expiry_date = $request->expiry_date;
            
            // Log expired stock replacement
            StockEditHistory::create([
                'product_id' => $request->Product_ID,
                'cashier_id' => auth()->id(),
                'action' => 'stock_in',
                'field_changed' => 'quantity',
                'old_value' => $oldQuantity . ' (expired)',
                'new_value' => $request->quantity,
                'remarks' => 'Expired stock replaced with new stock via Stock-In'
            ]);
            
            $message = 'Expired stock replaced with new stock! Old expired stock removed. New quantity: ' . $request->quantity;
        } else {
            // **Add to existing non-expired stock**
            $product->Quantity_in_Stock = $oldQuantity + $request->quantity;
            
            // **Update expiry date to EARLIEST date (FIFO principle)**
            if ($request->expiry_date) {
                if (!$product->expiry_date || $request->expiry_date < $product->expiry_date) {
                    $product->expiry_date = $request->expiry_date;
                }
            }
            
            // Log stock addition
            StockEditHistory::create([
                'product_id' => $request->Product_ID,
                'cashier_id' => auth()->id(),
                'action' => 'stock_in',
                'field_changed' => 'quantity',
                'old_value' => (string)$oldQuantity,
                'new_value' => (string)$product->Quantity_in_Stock,
                'remarks' => 'Added ' . $request->quantity . ' units via Stock-In'
            ]);
            
            $message = 'Stock added successfully! Quantity: ' . $request->quantity . ' added to inventory.';
        }
        
        // Update unit price with latest price
        $product->unit_price = $request->price;
        
        // Log price change ONLY if it's actually different
        if ($oldPrice != $request->price) {
            StockEditHistory::create([
                'product_id' => $request->Product_ID,
                'cashier_id' => auth()->id(),
                'action' => 'update',
                'field_changed' => 'unit_price',
                'old_value' => '₱' . number_format($oldPrice, 2),
                'new_value' => '₱' . number_format($request->price, 2),
                'remarks' => 'Price updated via Stock-In'
            ]);
        }
        
        // Log expiry date change if different
        if ($oldExpiryDate != $product->expiry_date) {
            StockEditHistory::create([
                'product_id' => $request->Product_ID,
                'cashier_id' => auth()->id(),
                'action' => 'update',
                'field_changed' => 'expiry_date',
                'old_value' => $oldExpiryDate ? \Carbon\Carbon::parse($oldExpiryDate)->format('M d, Y') : 'None',
                'new_value' => $product->expiry_date ? \Carbon\Carbon::parse($product->expiry_date)->format('M d, Y') : 'None',
                'remarks' => 'Expiry date updated via Stock-In'
            ]);
        }
        
        $product->save();

        return redirect()->route('stockins.index')
            ->with('success', $message);
    }

    public function edit(StockIn $stockin)
    {
        $products = Product::with('supplier')->get();

        // Calculate available supplier transactions for editing
        $latestSupplierTransactions = [];
        
        foreach ($products as $product) {
            // Find the most recent completed/paid transaction
            $latest = SupplierTransaction::where('Product_ID', $product->Product_ID)
                ->whereIn('status', ['completed', 'paid'])
                ->latest('Supply_transac_ID')
                ->first();
            
            if ($latest) {
                $suppliedQty = (float)$latest->quantity_units;
                
                // Exclude current record from sum
                $alreadyStockedQty = StockIn::where('supplier_transaction_id', $latest->Supply_transac_ID)
                    ->where('Stock_ID', '!=', $stockin->Stock_ID) 
                    ->sum('quantity');
                
                // Calculate remaining quantity available
                $remainingQty = $suppliedQty - $alreadyStockedQty;
                
                // Only show if there's remaining quantity
                if ($remainingQty > 0) {
                    $pricePerUnit = $suppliedQty > 0 ? ($latest->total_cost / $suppliedQty) : 0;
                    
                    $latestSupplierTransactions[$product->Product_ID] = [
                        'quantity' => $remainingQty,
                        'original_quantity' => $suppliedQty,
                        'already_stocked' => $alreadyStockedQty,
                        'price' => round($pricePerUnit, 2),
                        'date' => $latest->supply_date,
                        'transaction_id' => $latest->Supply_transac_ID,
                    ];
                }
            }
        }

        return view('stockins.edit', compact('stockin', 'products', 'latestSupplierTransactions'));
    }

    public function update(Request $request, StockIn $stockin)
    {
        $request->validate([
            'Product_ID' => 'required|exists:products,Product_ID',
            'date' => 'required|date',
            'quantity' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'unit' => 'required|string',
            'expiry_date' => 'nullable|date',
            'critical_level' => 'required|integer|min:0',
        ]);

        // **Store OLD values BEFORE making any changes**
        $oldQuantity = $stockin->quantity;
        $oldPrice = $stockin->price;
        $oldExpiryDate = $stockin->expiry_date;
        
        $newQuantity = $request->quantity;
        $newPrice = $request->price;
        $quantityDifference = $newQuantity - $oldQuantity;

        // Get product for audit logging
        $product = Product::findOrFail($request->Product_ID);
        $oldProductQuantity = $product->Quantity_in_Stock ?? 0;

        // Update the stock-in record
        $stockin->update($request->all());

        // Update product stock by the difference only
        $product->Quantity_in_Stock = $oldProductQuantity + $quantityDifference;
        
        // Log quantity change if different
        if ($quantityDifference != 0) {
            StockEditHistory::create([
                'product_id' => $request->Product_ID,
                'cashier_id' => auth()->id(),
                'action' => 'adjustment',
                'field_changed' => 'quantity',
                'old_value' => (string)$oldProductQuantity,
                'new_value' => (string)$product->Quantity_in_Stock,
                'remarks' => 'Stock-In updated: Quantity changed from ' . $oldQuantity . ' to ' . $newQuantity
            ]);
        }
        
        // **Recalculate earliest expiry date from all non-expired stock-ins**
        $earliestExpiry = StockIn::where('Product_ID', $product->Product_ID)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now()->startOfDay())
            ->orderBy('expiry_date', 'asc')
            ->first();
        
        $product->expiry_date = $earliestExpiry ? $earliestExpiry->expiry_date : null;
        
        // Log expiry date change if different
        if ($oldExpiryDate != $request->expiry_date) {
            StockEditHistory::create([
                'product_id' => $request->Product_ID,
                'cashier_id' => auth()->id(),
                'action' => 'update',
                'field_changed' => 'expiry_date',
                'old_value' => $oldExpiryDate ? \Carbon\Carbon::parse($oldExpiryDate)->format('M d, Y') : 'None',
                'new_value' => $request->expiry_date ? \Carbon\Carbon::parse($request->expiry_date)->format('M d, Y') : 'None',
                'remarks' => 'Expiry date updated via Stock-In edit'
            ]);
        }
        
        // Update unit price
        $product->unit_price = $newPrice;
        
        // Log price change ONLY if it's actually different
        if ((float)$oldPrice != (float)$newPrice) {
            StockEditHistory::create([
                'product_id' => $request->Product_ID,
                'cashier_id' => auth()->id(),
                'action' => 'update',
                'field_changed' => 'unit_price',
                'old_value' => '₱' . number_format($oldPrice, 2),
                'new_value' => '₱' . number_format($newPrice, 2),
                'remarks' => 'Price updated from ₱' . number_format($oldPrice, 2) . ' to ₱' . number_format($newPrice, 2) . ' via Stock-In edit'
            ]);
        }
        
        $product->save();

        return redirect()->route('stockins.index')
            ->with('success', 'Stock updated successfully.');
    }

    public function destroy(StockIn $stockin)
    {
        $productId = $stockin->Product_ID;
        $quantity = $stockin->quantity;
        
        // Get product for audit logging
        $product = Product::findOrFail($productId);
        $oldQuantity = $product->Quantity_in_Stock ?? 0;
        
        $stockin->delete();

        // **Subtract the deleted quantity from product stock**
        $product->Quantity_in_Stock = $oldQuantity - $quantity;
        
        // Log deletion in audit history
        StockEditHistory::create([
            'product_id' => $productId,
            'cashier_id' => auth()->id(),
            'action' => 'stock_out',
            'field_changed' => 'quantity',
            'old_value' => (string)$oldQuantity,
            'new_value' => (string)$product->Quantity_in_Stock,
            'remarks' => 'Stock-In record deleted. Removed ' . $quantity . ' units'
        ]);
        
        // **Recalculate earliest expiry date from remaining non-expired stock-ins**
        $earliestExpiry = StockIn::where('Product_ID', $product->Product_ID)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now()->startOfDay())
            ->orderBy('expiry_date', 'asc')
            ->first();
        
        $product->expiry_date = $earliestExpiry ? $earliestExpiry->expiry_date : null;
        
        $product->save();

        return redirect()->route('stockins.index')
            ->with('success', 'Stock deleted successfully.');
    }
}