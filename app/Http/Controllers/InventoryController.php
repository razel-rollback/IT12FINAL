<?php
// app/Http/Controllers/InventoryController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\StockEditHistory;
use App\Models\User;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::with(['supplier', 'stockIns'])->get();
        return view('inventory.index', compact('products'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('inventory.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Product_Name' => 'required|string|max:255',
            'Category' => 'required|string',
            'variety' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'Supplier_ID' => 'required|exists:suppliers,Supplier_ID',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Set initial values (will be updated by stock-ins)
        $validated['Quantity_in_Stock'] = 0;
        $validated['unit_price'] = 0;
        $validated['reorder_level'] = 5;

        $product = Product::create($validated);

        // Log the creation in audit history
        StockEditHistory::create([
            'product_id' => $product->Product_ID,
            'cashier_id' => auth()->id(),
            'action' => 'create',
            'field_changed' => 'product_created',
            'old_value' => null,
            'new_value' => $product->Product_Name,
            'remarks' => 'Product created'
        ]);

        return redirect()->route('inventory.index')
            ->with('success', 'Product added successfully! Now add stock for this product.');
    }

    public function edit(Product $inventory)
    {
        $suppliers = Supplier::all();
        return view('inventory.edit', compact('inventory', 'suppliers'));
    }

    public function update(Request $request, Product $inventory)
    {
        $validated = $request->validate([
            'Product_Name' => 'required|string|max:255',
            'Category' => 'required|string',
            'variety' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'Supplier_ID' => 'required|exists:suppliers,Supplier_ID',
        ]);

        // Track changes for audit log
        $changes = [];
        foreach ($validated as $key => $value) {
            if ($key !== 'image' && $inventory->$key != $value) {
                $changes[] = [
                    'field' => $key,
                    'old' => $inventory->$key,
                    'new' => $value
                ];
            }
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
            $changes[] = [
                'field' => 'image',
                'old' => $inventory->image,
                'new' => $validated['image']
            ];
        }

        $inventory->update($validated);

        // Log each change in audit history
        foreach ($changes as $change) {
            StockEditHistory::create([
                'product_id' => $inventory->Product_ID,
                'cashier_id' => auth()->id(),
                'action' => 'update',
                'field_changed' => $change['field'],
                'old_value' => $change['old'],
                'new_value' => $change['new'],
                'remarks' => 'Product information updated'
            ]);
        }

        return redirect()->route('inventory.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $inventory)
    {
        // Log deletion in audit history
        StockEditHistory::create([
            'product_id' => $inventory->Product_ID,
            'cashier_id' => auth()->id(),
            'action' => 'delete',
            'field_changed' => 'product_deleted',
            'old_value' => $inventory->Product_Name,
            'new_value' => null,
            'remarks' => 'Product deleted'
        ]);

        $inventory->delete();
        
        return redirect()->route('inventory.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Display audit log for all products
     */
    public function audit()
    {
        // Get all edit history across all products
        $auditLogs = StockEditHistory::with(['product', 'cashier'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('inventory.audit', compact('auditLogs'));
    }

    /**
     * Display edit history for a specific product
     */
    public function editHistory($productId)
    {
        $inventory = Product::findOrFail($productId);
        
        // Get all users for filter dropdown
        $cashiers = User::all();
        
        // Build the query for edit history
        $query = StockEditHistory::where('product_id', $productId)
            ->with('cashier');
        
        // Apply filters if present
        if (request('date_from')) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }
        
        if (request('date_to')) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }
        
        if (request('cashier_id')) {
            $query->where('cashier_id', request('cashier_id'));
        }
        
        $editHistory = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Calculate statistics
        $statistics = [
            'total_stock_in' => StockEditHistory::where('product_id', $productId)
                ->where('action', 'stock_in')->count(),
            'total_stock_out' => StockEditHistory::where('product_id', $productId)
                ->where('action', 'stock_out')->count(),
            'total_adjustments' => StockEditHistory::where('product_id', $productId)
                ->where('action', 'adjustment')->count(),
        ];
        
        return view('inventory.edit-history', compact('inventory', 'editHistory', 'cashiers', 'statistics'));
    }
}