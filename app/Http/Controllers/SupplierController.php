<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierTransaction;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // -----------------------------
    // SUPPLIERS CRUD
    // -----------------------------
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $suppliers = Supplier::query()
            ->when($search, function ($query, $search) {
                return $query->where('Supplier_Name', 'like', "%{$search}%")
                            ->orWhere('contact_person', 'like', "%{$search}%")
                            ->orWhere('contact_number', 'like', "%{$search}%");
            })
            ->orderBy('Supplier_Name', 'asc')
            ->get();
        
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Supplier_Name' => 'required',
            'contact_person' => 'required',
            'contact_number' => 'required',
            'address' => 'required',
            'payment_terms' => 'required',
        ]);

        Supplier::create($request->all());
        return redirect()->route('suppliers.index')->with('success', 'Supplier added successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'Supplier_Name' => 'required',
            'contact_person' => 'required',
            'contact_number' => 'required',
            'address' => 'required',
            'payment_terms' => 'required',
        ]);

        $supplier->update($request->all());
        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    // Soft Delete (Move to Archive)
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier archived successfully.');
    }

    // -----------------------------
    // ARCHIVE FUNCTIONS
    // -----------------------------

    // Show all archived suppliers
    public function archive()
    {
        $suppliers = Supplier::onlyTrashed()->get();
        return view('suppliers.archive', compact('suppliers'));
    }

    // Restore archived supplier
    public function restore($id)
    {
        $supplier = Supplier::withTrashed()->findOrFail($id);
        $supplier->restore();

        return redirect()->route('archive.suppliers')->with('success', 'Supplier restored successfully.');
    }

    // Permanently delete supplier
    public function forceDelete($id)
    {
        $supplier = Supplier::withTrashed()->findOrFail($id);
        $supplier->forceDelete();

        return redirect()->route('archive.suppliers')->with('success', 'Supplier permanently deleted.');
    }

    // -----------------------------
    // SUPPLIER TRANSACTIONS
    // -----------------------------
    public function transactions()
    {
        $transactions = SupplierTransaction::with(['supplier', 'product'])->latest()->get();
        return view('suppliers.transactions', compact('transactions'));
    }
}