<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        // Load customers with all sales
        $customers = Customer::with('sales')->get();
        return view('customers.index', compact('customers'));
    }

    public function show($id)
    {
        $customer = Customer::with([
            'sales' => function($query) {
                $query->orderBy('transaction_date', 'desc');
            },
            'sales.details.product',
            'sales.user'
        ])->findOrFail($id);
        
        return view('customers.show', compact('customer'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Customer_Name' => 'required',
            'Contact_Number' => 'nullable',
        ]);

        Customer::create($request->all());
        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Customer_Name' => 'required',
            'Contact_Number' => 'nullable',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->all());
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    // ============================
    // SOFT DELETE (Archive)
    // ============================
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete(); // soft delete
        return redirect()->route('customers.index')->with('success', 'Customer archived successfully.');
    }

    // ============================
    // ARCHIVE PAGE
    // ============================
    public function archive()
    {
        $customers = Customer::onlyTrashed()->get();
        return view('customers.archive', compact('customers'));
    }

    // ============================
    // RESTORE CUSTOMER
    // ============================
    public function restore($id)
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $customer->restore();

        return redirect()->route('archive.customers')->with('success', 'Customer restored successfully.');
    }

    // ============================
    // PERMANENT DELETE
    // ============================
    public function forceDelete($id)
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $customer->forceDelete();

        return redirect()->route('archive.customers')->with('success', 'Customer permanently deleted.');
    }
}
