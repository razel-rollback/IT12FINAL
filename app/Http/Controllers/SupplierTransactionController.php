<?php
// app/Http/Controllers/SupplierTransactionController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierTransaction;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\StockIn;

class SupplierTransactionController extends Controller
{
    private function loadSupplierProducts()
    {
        $suppliers = Supplier::with('products')->get();

        $productsBySupplier = [];
        foreach ($suppliers as $supplier) {
            $productsBySupplier[$supplier->Supplier_ID] = $supplier->products->map(function ($product) {
                $displayName = $product->Product_Name;
                if (!empty($product->variety)) {
                    $displayName .= ' - ' . $product->variety;
                }
                
                return [
                    'Product_ID' => $product->Product_ID,
                    'name' => $displayName,
                    'variety' => $product->variety ?? 'N/A',
                    'unit_weight' => $product->unit_weight ?? 0,
                ];
            });
        }

        return compact('suppliers', 'productsBySupplier');
    }

    public function index()
    {
        $transactions = SupplierTransaction::with(['supplier', 'product'])
            ->orderBy('Supply_transac_ID', 'DESC')
            ->get();

        return view('supplier_transactions.index', compact('transactions'));
    }

    public function create()
    {
        $data = $this->loadSupplierProducts();
        return view('supplier_transactions.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'Supplier_ID' => 'required|exists:suppliers,Supplier_ID',
            'Product_ID' => 'required|exists:products,Product_ID',
            'supplier_price' => 'required|numeric|min:0',
            'quantity_units' => 'required|numeric|min:0',
            'quantity_kilos' => 'required|numeric|min:0',
            'supply_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        // Check if there's already a PAID transaction with the same supplier and product
        $existingPaidTransaction = SupplierTransaction::where('Supplier_ID', $request->Supplier_ID)
            ->where('Product_ID', $request->Product_ID)
            ->where('status', 'paid')
            ->exists();

        if ($existingPaidTransaction) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'Product_ID' => 'This supplier already has a paid transaction for this product. Please wait until they supply more stock or choose a different product.'
                ]);
        }

        

        // Get product to access unit_weight
        $product = Product::findOrFail($request->Product_ID);
        $unitWeight = $product->unit_weight ?? 0;

        // Calculate total kilos: quantity_kilos + (quantity_units * unit_weight)
        $totalKilos = $request->quantity_kilos + ($request->quantity_units * $unitWeight);
        
        // Calculate total_cost: total_kilos * supplier_price
        $total_cost = $totalKilos * $request->supplier_price;

        $transaction = SupplierTransaction::create([
            'Supplier_ID' => $request->Supplier_ID,
            'Product_ID' => $request->Product_ID,
            'supplier_price' => $request->supplier_price,
            'quantity_units' => $request->quantity_units,
            'quantity_kilos' => $request->quantity_kilos,
            'supply_date' => $request->supply_date,
            'total_cost' => $total_cost,
            'status' => $request->status,
        ]);
        
        if ($request->status === 'completed') {
            return redirect()->route('supplier.transactions')
                ->with('success', 'Transaction completed! Please go to Stock In → Add Stock to update inventory.');
        }

        return redirect()->route('supplier.transactions')
            ->with('success', 'Transaction added successfully.');
    }

    public function edit(SupplierTransaction $supplier_transaction)
    {
        $data = $this->loadSupplierProducts();

        return view('supplier_transactions.edit', 
            array_merge($data, ['supplier_transaction' => $supplier_transaction])
        );
    }

    

    public function update(Request $request, SupplierTransaction $supplier_transaction)
    {
        $request->validate([
            'Supplier_ID' => 'required|exists:suppliers,Supplier_ID',
            'Product_ID' => 'required|exists:products,Product_ID',
            'supplier_price' => 'required|numeric|min:0',
            'quantity_units' => 'required|numeric|min:0',
            'quantity_kilos' => 'required|numeric|min:0',
            'supply_date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled,paid',
        ]);

        // Check if trying to create duplicate paid transaction
        // (Only check if supplier or product is being changed)
        if ($request->Supplier_ID != $supplier_transaction->Supplier_ID || 
            $request->Product_ID != $supplier_transaction->Product_ID) {
            
            $existingPaidTransaction = SupplierTransaction::where('Supplier_ID', $request->Supplier_ID)
                ->where('Product_ID', $request->Product_ID)
                ->where('status', 'paid')
                ->where('Supply_transac_ID', '!=', $supplier_transaction->Supply_transac_ID)
                ->exists();

            if ($existingPaidTransaction) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors([
                        'Product_ID' => 'This supplier already has a paid transaction for this product. Cannot change to this combination.'
                    ]);
            }
        }

        // Get product to access unit_weight
        $product = Product::findOrFail($request->Product_ID);
        $unitWeight = $product->unit_weight ?? 0;

        // Calculate total kilos: quantity_kilos + (quantity_units * unit_weight)
        $totalKilos = $request->quantity_kilos + ($request->quantity_units * $unitWeight);
        
        // Calculate total_cost: total_kilos * supplier_price
        $total_cost = $totalKilos * $request->supplier_price;

        // Get old status before update
        $oldStatus = $supplier_transaction->status;
        
        $supplier_transaction->update([
            'Supplier_ID' => $request->Supplier_ID,
            'Product_ID' => $request->Product_ID,
            'supplier_price' => $request->supplier_price,
            'quantity_units' => $request->quantity_units,
            'quantity_kilos' => $request->quantity_kilos,
            'supply_date' => $request->supply_date,
            'total_cost' => $total_cost,
            'status' => $request->status,
        ]);
        
        if ($oldStatus !== 'completed' && $request->status === 'completed') {
            return redirect()->route('supplier.transactions')
                ->with('success', 'Transaction marked as completed! Please go to Stock In → Add Stock to update inventory.');
        }

        return redirect()->route('supplier.transactions')
            ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(SupplierTransaction $supplier_transaction)
    {
        // Prevent deletion of paid transactions
        if ($supplier_transaction->status === 'paid') {
            return redirect()->back()
                ->with('error', 'Cannot delete a paid transaction. Please cancel it first if needed.');
        }

        $supplier_transaction->delete();

        return redirect()->route('supplier.transactions')
            ->with('success', 'Transaction deleted successfully.');
    }

    public function pay(SupplierTransaction $supplier_transaction)
    {
        if ($supplier_transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending transactions can be paid.');
        }

        // Check if there's already another PAID transaction with same supplier and product
        $existingPaidTransaction = SupplierTransaction::where('Supplier_ID', $supplier_transaction->Supplier_ID)
            ->where('Product_ID', $supplier_transaction->Product_ID)
            ->where('status', 'paid')
            ->where('Supply_transac_ID', '!=', $supplier_transaction->Supply_transac_ID)
            ->exists();

        if ($existingPaidTransaction) {
            return redirect()->back()
                ->with('error', 'There is already a paid transaction for this supplier and product combination. Cannot mark as paid.');
        }

        $supplier_transaction->update([
            'status' => 'paid'
        ]);

        return redirect()->route('supplier.transactions')
            ->with('success', 'Transaction marked as paid. This supplier cannot create another transaction for this product until new stock is supplied.');
    }

    public function printReceipt(SupplierTransaction $supplier_transaction)
    {
        $supplier_transaction->load(['supplier', 'product']);

        return view('supplier_transactions.receipt', [
            'transaction' => $supplier_transaction
        ]);
    }
    
}