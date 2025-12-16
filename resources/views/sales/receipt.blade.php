@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 rounded shadow-lg">
    <!-- Company Header -->
    <div class="text-center mb-6 border-b-2 border-green-700 pb-4">
        <h1 class="text-3xl font-bold text-green-700">CRM FruitStand</h1>
        <p class="text-sm text-gray-600 mt-1">Fresh Fruits & Quality Service</p>
        <p class="text-xs text-gray-500">Davao City, Philippines</p>
        <p class="text-xs text-gray-500">Contact: (082) XXX-XXXX</p>
    </div>

    <h2 class="text-xl font-bold mb-4 text-center text-gray-800">SALES RECEIPT</h2>

    <!-- Receipt Details -->
    <div class="mb-4 text-sm">
        <div class="grid grid-cols-2 gap-2">
            <div>
                <p><strong>Receipt No:</strong></p>
                <p class="text-gray-700">{{ $sale->receipt_number }}</p>
            </div>
            <div class="text-right">
                <p><strong>Date:</strong></p>
                <p class="text-gray-700">{{ \Carbon\Carbon::parse($sale->transaction_date)->format('M d, Y') }}</p>
                <p class="text-gray-700">{{ \Carbon\Carbon::parse($sale->transaction_date)->format('h:i A') }}</p>
            </div>
        </div>
        
        <div class="mt-3">
            <p>
                <strong>Customer:</strong> 
                @if($sale->customer)
                    {{ $sale->customer->Customer_Name }}
                @else
                    <span class="text-gray-500 italic">Walk-in Customer</span>
                @endif
            </p>
            <p><strong>Cashier:</strong> {{ $sale->user->fname }} {{ $sale->user->lname }}</p>
            <p><strong>Payment:</strong> {{ ucfirst($sale->payment_method) }}</p>
        </div>
    </div>

    <hr class="my-3 border-gray-300">

    <!-- Items Table -->
    <table class="w-full text-left mb-4 text-sm">
        <thead>
            <tr class="border-b-2 border-gray-400">
                <th class="py-2">Item</th>
                <th class="py-2 text-center">Qty</th>
                <th class="py-2 text-right">Price</th>
                <th class="py-2 text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->details as $detail)
            <tr class="border-b border-gray-200">
                <td class="py-2">
                    <div class="font-semibold">{{ $detail->product->Product_Name }}</div>
                    @if($detail->product->variety)
                        <small class="text-gray-600">{{ $detail->product->variety }}</small>
                    @endif
                </td>
                <td class="py-2 text-center">{{ $detail->Quantity }} kg</td>
                <td class="py-2 text-right">₱{{ number_format($detail->unit_price, 2) }}</td>
                <td class="py-2 text-right font-semibold">₱{{ number_format($detail->Quantity * $detail->unit_price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr class="my-3 border-t-2 border-black">
    
    <!-- Total Section -->
    <div class="text-right mb-4">
        <div class="flex justify-between items-center mb-2">
            <span class="text-lg font-semibold">TOTAL:</span>
            <span class="text-2xl font-bold text-green-700">₱{{ number_format($sale->total_amount, 2) }}</span>
        </div>
        <p class="text-sm text-gray-600">
            Status: 
            <span class="font-semibold px-2 py-1 rounded {{ $sale->status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                {{ ucfirst($sale->status) }}
            </span>
        </p>
    </div>

    <!-- Action Buttons (Hidden on Print) -->
    <div class="flex justify-center mt-6 gap-2 print:hidden">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition shadow">
            <span class="material-icons align-middle" style="font-size: 18px;">print</span>
            Print Receipt
        </button>
        <a href="{{ route('sales.index') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition inline-block shadow">
            <span class="material-icons align-middle" style="font-size: 18px;">arrow_back</span>
            Back to Sales
        </a>
    </div>

    <!-- Footer -->
    <div class="text-center mt-6 pt-4 border-t border-gray-300 text-xs text-gray-500">
        <p class="font-semibold mb-1">Thank you for your purchase!</p>
        <p>Fresh fruits delivered with care</p>
        <p class="mt-2">Please come again!</p>
        <p class="mt-3 text-gray-400">This serves as your official receipt</p>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .max-w-md, .max-w-md * {
            visibility: visible;
        }
        .max-w-md {
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