@extends('layouts.app')

@section('content')
<h1 class="text-3xl font-bold mb-6 text-green-700">Edit Stock Record</h1>

@if ($errors->any())
<div class="bg-red-100 text-red-700 p-2 rounded mb-4">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm text-blue-700">
                <strong>Note:</strong> Quantity cannot be changed here to preserve inventory history. 
                If you need to add more stock, please use the <a href="{{ route('stockins.create') }}" class="underline font-bold">Add Stock</a> page.
            </p>
        </div>
    </div>
</div>

<form action="{{ route('stockins.update', $stockin->Stock_ID) }}" method="POST" class="bg-white p-6 rounded shadow">
    @csrf
    @method('PUT')
    
    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-2">Product</label>
        <select name="Product_ID" class="w-full border p-2 rounded bg-gray-100 cursor-not-allowed pointer-events-none" readonly>
            @foreach($products as $product)
                <option value="{{ $product->Product_ID }}" 
                    {{ old('Product_ID', $stockin->Product_ID) == $product->Product_ID ? 'selected' : '' }}>
                    {{ $product->Product_Name }}@if($product->variety) - {{ $product->variety }}@endif ({{ $product->Category }})
                </option>
            @endforeach
        </select>
        <input type="hidden" name="Product_ID" value="{{ $stockin->Product_ID }}">
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-2">Date</label>
        <input type="date" name="date" 
            value="{{ old('date', $stockin->date->format('Y-m-d')) }}" 
            class="w-full border p-2 rounded" required>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-2">Quantity</label>
        <input type="number" step="0.01" name="quantity" 
            value="{{ old('quantity', $stockin->quantity) }}" 
            class="w-full border p-2 rounded bg-gray-100 text-gray-500 cursor-not-allowed" 
            readonly>
        <p class="text-xs text-gray-500 mt-1">Quantity is locked for data integrity.</p>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-2">Price per Unit</label>
        <input type="number" step="0.01" name="price" 
            value="{{ old('price', $stockin->price) }}" 
            class="w-full border p-2 rounded" min="0" required>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-2">Unit (e.g., kg, pcs, box)</label>
        <input type="text" name="unit" 
            value="{{ old('unit', $stockin->unit) }}" 
            class="w-full border p-2 rounded" required>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-2">Expiry Date (Optional)</label>
        <input type="date" name="expiry_date" 
            value="{{ old('expiry_date', $stockin->expiry_date ? $stockin->expiry_date->format('Y-m-d') : '') }}" 
            class="w-full border p-2 rounded"
            min="{{ date('Y-m-d', strtotime('+1 day')) }}">
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 font-semibold mb-2">Critical Level</label>
        <input type="number" name="critical_level" 
            value="{{ old('critical_level', $stockin->critical_level) }}" 
            class="w-full border p-2 rounded" min="0" required>
    </div>

    <div class="flex gap-2">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update Record</button>
        <a href="{{ route('stockins.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</a>
    </div>
</form>
@endsection