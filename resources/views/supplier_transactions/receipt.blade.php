@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto bg-white p-6 rounded shadow-lg">
    <!-- Company Header -->
    <div class="text-center mb-6 border-b-2 border-green-700 pb-4">
        <h1 class="text-3xl font-bold text-green-700">CRM FruitStand</h1>
        <p class="text-sm text-gray-600 mt-1">Fresh Fruits & Quality Service</p>
        <p class="text-xs text-gray-500">Davao City, Philippines</p>
        <p class="text-xs text-gray-500">Contact: (082) XXX-XXXX</p>
    </div>

    <h2 class="text-xl font-bold mb-4 text-center text-gray-800">SUPPLIER TRANSACTION RECEIPT</h2>

    <!-- Transaction Details -->
    <div class="bg-gray-50 p-4 rounded mb-4">
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <p class="text-gray-600">Transaction ID:</p>
                <p class="font-semibold">#{{ $transaction->Supply_transac_ID }}</p>
            </div>
            <div class="text-right">
                <p class="text-gray-600">Date:</p>
                <p class="font-semibold">{{ \Carbon\Carbon::parse($transaction->supply_date)->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Supplier Information -->
    <div class="mb-4 border-b border-gray-200 pb-4">
        <h3 class="font-bold text-gray-700 mb-2">Supplier Information</h3>
        <div class="text-sm space-y-1">
            <p><strong>Name:</strong> {{ $transaction->supplier->Supplier_Name }}</p>
            @if($transaction->supplier->Contact_Number)
                <p><strong>Contact:</strong> {{ $transaction->supplier->Contact_Number }}</p>
            @endif
            @if($transaction->supplier->Address)
                <p><strong>Address:</strong> {{ $transaction->supplier->Address }}</p>
            @endif
        </div>
    </div>

    <!-- Product Details -->
    <div class="mb-4">
        <h3 class="font-bold text-gray-700 mb-2">Product Details</h3>
        <table class="w-full text-sm">
            <tbody>
                <tr class="border-b">
                    <td class="py-2 font-semibold">Product Name:</td>
                    <td class="py-2 text-right">{{ $transaction->product->Product_Name }}</td>
                </tr>
                @if($transaction->product->variety)
                <tr class="border-b">
                    <td class="py-2 font-semibold">Variety:</td>
                    <td class="py-2 text-right">{{ $transaction->product->variety }}</td>
                </tr>
                @endif
                <tr class="border-b">
                    <td class="py-2 font-semibold">Quantity (Units):</td>
                    <td class="py-2 text-right">{{ number_format($transaction->quantity_units, 2) }}</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 font-semibold">Quantity (Kilos):</td>
                    <td class="py-2 text-right">{{ number_format($transaction->quantity_kilos, 2) }} kg</td>
                </tr>
                <tr class="border-b">
                    <td class="py-2 font-semibold">Price per Unit:</td>
                    <td class="py-2 text-right">₱{{ number_format($transaction->total_cost / $transaction->quantity_units, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Total Section -->
    <div class="bg-green-50 p-4 rounded mb-4 border-2 border-green-700">
        <div class="flex justify-between items-center">
            <span class="text-lg font-bold text-gray-700">TOTAL COST:</span>
            <span class="text-2xl font-bold text-green-700">₱{{ number_format($transaction->total_cost, 2) }}</span>
        </div>
        <div class="mt-2 text-right">
            <span class="text-sm text-gray-600">Status: </span>
            <span class="font-semibold px-3 py-1 rounded text-sm
                {{ $transaction->status == 'paid' ? 'bg-green-100 text-green-700' : '' }}
                {{ $transaction->status == 'completed' ? 'bg-blue-100 text-blue-700' : '' }}
                {{ $transaction->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                {{ $transaction->status == 'cancelled' ? 'bg-red-100 text-red-700' : '' }}">
                {{ ucfirst($transaction->status) }}
            </span>
        </div>
    </div>

    <!-- Payment Information (if paid) -->
    @if($transaction->status == 'paid' && $transaction->payment_date)
    <div class="mb-4 text-sm bg-blue-50 p-3 rounded">
        <p><strong>Payment Date:</strong> {{ \Carbon\Carbon::parse($transaction->payment_date)->format('M d, Y') }}</p>
        @if($transaction->payment_method)
            <p><strong>Payment Method:</strong> {{ ucfirst($transaction->payment_method) }}</p>
        @endif
    </div>
    @endif

    <!-- Notes -->
    @if($transaction->notes)
    <div class="mb-4 text-sm">
        <p class="font-bold text-gray-700">Notes:</p>
        <p class="text-gray-600 bg-gray-50 p-2 rounded">{{ $transaction->notes }}</p>
    </div>
    @endif

    <!-- Action Buttons (Hidden on Print) -->
    <div class="flex justify-center mt-6 gap-2 print:hidden">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition shadow">
            <span class="material-icons align-middle" style="font-size: 18px;">print</span>
            Print Receipt
        </button>
        <a href="{{ route('supplier.transactions') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition inline-block shadow">
            <span class="material-icons align-middle" style="font-size: 18px;">arrow_back</span>
            Back to Transactions
        </a>
    </div>

    <!-- Footer -->
    <div class="text-center mt-6 pt-4 border-t border-gray-300 text-xs text-gray-500">
        <p class="font-semibold mb-1">Official Supplier Transaction Receipt</p>
        <p>CRM FruitStand - Supplier Management</p>
        <p class="mt-2 text-gray-400">This document serves as proof of transaction</p>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .max-w-lg, .max-w-lg * {
            visibility: visible;
        }
        .max-w-lg {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none;
        }
        .print\:hidden {
            display: none !important;
        }
        /* Ensure clean print layout */
        @page {
            margin: 1cm;
        }
    }
</style>
@endsection