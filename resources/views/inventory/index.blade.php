@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 class="text-3xl font-extrabold text-green-700">Inventory</h1>
        <div class="flex gap-3">
            <a href="{{ route('inventory.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-semibold shadow-md transition transform hover:scale-105">
                + Add Product
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-3">
        <div class="flex gap-2">
            <div class="flex-1 max-w-md">
                <input type="text" 
                       id="searchInput"
                       placeholder="Search products..." 
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md animate-fade">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-green-700 text-white rounded-t-xl">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Product Name</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Category</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Variety</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Description</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Image</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Stock</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Unit Price</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Expiry Date</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Supplier</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white" id="productsTableBody">
                @forelse($products as $product)
                <tr class="hover:bg-green-50 transition product-row" 
                    data-product-name="{{ strtolower($product->Product_Name) }}" 
                    data-category="{{ strtolower($product->Category) }}"
                    data-variety="{{ strtolower($product->variety ?? '') }}"
                    data-supplier="{{ strtolower($product->supplier ? $product->supplier->Supplier_Name : '') }}">
                    
                    <td class="py-2 px-4 text-sm font-medium text-gray-800">{{ $product->Product_Name }}</td>
                    <td class="py-2 px-4 text-sm text-gray-700">{{ $product->Category }}</td>
                    <td class="py-2 px-4 text-sm text-gray-700">{{ $product->variety ?? 'N/A' }}</td>
                    <td class="py-2 px-4 text-sm text-gray-700">{{ $product->description ?? 'N/A' }}</td>
                    <td class="py-2 px-4">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->Product_Name }}" class="w-16 h-16 object-cover rounded">
                        @else
                            <span class="text-gray-400 text-sm">N/A</span>
                        @endif
                    </td>
                    <td class="py-2 px-4">
                        <span class="font-semibold {{ $product->Quantity_in_Stock <= 10 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $product->Quantity_in_Stock }}
                        </span>
                    </td>
                    <td class="py-2 px-4 text-sm text-gray-700">₱{{ number_format($product->unit_price, 2) }}</td>
                    <td class="py-2 px-4 text-sm">
                        @if($product->expiry_date)
                        @php
                            $expiryDate = \Carbon\Carbon::parse($product->expiry_date);
                            $daysLeft = \Carbon\Carbon::now()->startOfDay()->diffInDays($expiryDate->startOfDay(), false);
                            $isExpiringSoon = $daysLeft > 0 && $daysLeft <= 7;
                            $isExpired = $daysLeft <= 0;
                        @endphp
                            
                            @if($isExpired)
                                <span class="text-red-600 font-bold">Expired</span>
                            @elseif($isExpiringSoon)
                                <span class="text-orange-600 font-semibold">
                                    {{ $expiryDate->format('M d, Y') }}
                                    <span class="block text-xs">({{ $daysLeft }} days)</span>
                                </span>
                            @else
                                <span class="text-gray-600">
                                    {{ $expiryDate->format('M d, Y') }}
                                </span>
                            @endif
                        @else
                            <span class="text-gray-400 text-sm">N/A</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 text-sm text-gray-700">{{ $product->supplier ? $product->supplier->Supplier_Name : 'N/A' }}</td>
                    
                    <td class="py-2 px-4 flex gap-3">
                        <a href="{{ route('inventory.edit', $product->Product_ID) }}"
                           class="text-yellow-600 hover:text-yellow-700 transition transform hover:scale-110" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 113.182 3.183L7.5 19.215 3 21l1.784-4.5 12.078-13.013z" />
                            </svg>
                        </a>
                        <form action="{{ route('inventory.destroy', $product->Product_ID) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700 transition transform hover:scale-110" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr id="noResults">
                    <td colspan="10" class="py-8 px-4 text-center text-gray-500">
                        No products available
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div id="paginationControls" class="px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6">
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium" id="pageStart">1</span> to <span class="font-medium" id="pageEnd">10</span> of <span class="font-medium" id="totalItems">0</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button id="prevBtn" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <div id="numberedButtonsContainer" class="flex">
                            </div>

                        <button id="nextBtn" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </nav>
                </div>
            </div>
            <div class="flex-1 flex justify-between sm:hidden">
                <button id="mobilePrevBtn" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</button>
                <button id="mobileNextBtn" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('productsTableBody');
    const rows = Array.from(tableBody.querySelectorAll('tr.product-row')); 
    const noResults = document.getElementById('noResults');
    const numberedButtonsContainer = document.getElementById('numberedButtonsContainer');
    
    // Pagination Variables
    const itemsPerPage = 10;
    let currentPage = 1;
    let filteredRows = rows;

    const pageStart = document.getElementById('pageStart');
    const pageEnd = document.getElementById('pageEnd');
    const totalItems = document.getElementById('totalItems');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const mobilePrevBtn = document.getElementById('mobilePrevBtn');
    const mobileNextBtn = document.getElementById('mobileNextBtn');

    function updateTable() {
        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / itemsPerPage);

        // Validation
        if (currentPage < 1) currentPage = 1;
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        // Display Logic
        rows.forEach(row => row.style.display = 'none');

        if (totalRows > 0) {
            filteredRows.slice(start, end).forEach(row => row.style.display = '');
            noResults.style.display = 'none';
        } else {
            noResults.style.display = '';
            if (searchInput.value.trim() !== '') {
                noResults.querySelector('td').textContent = 'No products found matching "' + searchInput.value + '"';
            } else {
                noResults.querySelector('td').textContent = 'No products available';
            }
        }

        // Stats
        totalItems.textContent = totalRows;
        pageStart.textContent = totalRows === 0 ? 0 : start + 1;
        pageEnd.textContent = Math.min(end, totalRows);

        // Update Buttons (Enable/Disable)
        const isFirst = currentPage === 1;
        const isLast = currentPage === totalPages || totalPages === 0;

        prevBtn.disabled = isFirst;
        mobilePrevBtn.disabled = isFirst;
        nextBtn.disabled = isLast;
        mobileNextBtn.disabled = isLast;

        [prevBtn, nextBtn, mobilePrevBtn, mobileNextBtn].forEach(btn => {
            if (btn.disabled) {
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });

        // ---------------------------------------------------------
        //  DYNAMIC NUMBERED BUTTONS GENERATOR
        // ---------------------------------------------------------
        numberedButtonsContainer.innerHTML = ''; // Clear existing numbers

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            // Base classes for pagination numbers
            let classes = "relative inline-flex items-center px-4 py-2 border text-sm font-medium";
            
            if (i === currentPage) {
                // Active Page Style (Green Background)
                classes += " z-10 bg-green-50 border-green-500 text-green-600";
            } else {
                // Inactive Page Style (White Background)
                classes += " bg-white border-gray-300 text-gray-500 hover:bg-gray-50";
            }
            
            btn.className = classes;
            
            // Add Click Event
            btn.addEventListener('click', function() {
                currentPage = i;
                updateTable();
            });

            numberedButtonsContainer.appendChild(btn);
        }
    }

    // Navigation Events
    function changePage(delta) {
        currentPage += delta;
        updateTable();
    }

    prevBtn.addEventListener('click', () => changePage(-1));
    nextBtn.addEventListener('click', () => changePage(1));
    mobilePrevBtn.addEventListener('click', () => changePage(-1));
    mobileNextBtn.addEventListener('click', () => changePage(1));

    // Search Events
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        filteredRows = rows.filter(row => {
            const productName = row.getAttribute('data-product-name') || '';
            const category = row.getAttribute('data-category') || '';
            const variety = row.getAttribute('data-variety') || '';
            const supplier = row.getAttribute('data-supplier') || '';
            return productName.includes(searchTerm) || category.includes(searchTerm) || variety.includes(searchTerm) || supplier.includes(searchTerm);
        });
        currentPage = 1; // Reset to page 1 on search
        updateTable();
    });

    // Init
    updateTable();
});
</script>
@endsection

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 class="text-3xl font-extrabold text-green-700">Inventory</h1>
        <div class="flex gap-3">
            <a href="{{ route('inventory.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-semibold shadow-md transition transform hover:scale-105">
                + Add Product
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-3">
        <div class="flex gap-2">
            <div class="flex-1 max-w-md">
                <input type="text" 
                       id="searchInput"
                       placeholder="Search products..." 
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md animate-fade">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-green-700 text-white rounded-t-xl">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Product Name</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Category</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Variety</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Description</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Image</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Stock</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Unit Price</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Expiry Date</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Supplier</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white" id="productsTableBody">
                @forelse($products as $product)
                <tr class="hover:bg-green-50 transition product-row" 
                    data-product-name="{{ strtolower($product->Product_Name) }}" 
                    data-category="{{ strtolower($product->Category) }}"
                    data-variety="{{ strtolower($product->variety ?? '') }}"
                    data-supplier="{{ strtolower($product->supplier ? $product->supplier->Supplier_Name : '') }}">
                    
                    <td class="py-2 px-4 text-sm font-medium text-gray-800">{{ $product->Product_Name }}</td>
                    <td class="py-2 px-4 text-sm text-gray-700">{{ $product->Category }}</td>
                    <td class="py-2 px-4 text-sm text-gray-700">{{ $product->variety ?? 'N/A' }}</td>
                    <td class="py-2 px-4 text-sm text-gray-700">{{ $product->description ?? 'N/A' }}</td>
                    <td class="py-2 px-4">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->Product_Name }}" class="w-16 h-16 object-cover rounded">
                        @else
                            <span class="text-gray-400 text-sm">N/A</span>
                        @endif
                    </td>
                    <td class="py-2 px-4">
                        <span class="font-semibold {{ $product->Quantity_in_Stock <= 10 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $product->Quantity_in_Stock }}
                        </span>
                    </td>
                    <td class="py-2 px-4 text-sm text-gray-700">₱{{ number_format($product->unit_price, 2) }}</td>
                    <td class="py-2 px-4 text-sm">
                        @if($product->expiry_date)
                        @php
                            $expiryDate = \Carbon\Carbon::parse($product->expiry_date);
                            $daysLeft = \Carbon\Carbon::now()->startOfDay()->diffInDays($expiryDate->startOfDay(), false);
                            $isExpiringSoon = $daysLeft > 0 && $daysLeft <= 7;
                            $isExpired = $daysLeft <= 0;
                        @endphp
                            
                            @if($isExpired)
                                <span class="text-red-600 font-bold">Expired</span>
                            @elseif($isExpiringSoon)
                                <span class="text-orange-600 font-semibold">
                                    {{ $expiryDate->format('M d, Y') }}
                                    <span class="block text-xs">({{ $daysLeft }} days)</span>
                                </span>
                            @else
                                <span class="text-gray-600">
                                    {{ $expiryDate->format('M d, Y') }}
                                </span>
                            @endif
                        @else
                            <span class="text-gray-400 text-sm">N/A</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 text-sm text-gray-700">{{ $product->supplier ? $product->supplier->Supplier_Name : 'N/A' }}</td>
                    
                    <td class="py-2 px-4 flex gap-3">
                        <a href="{{ route('inventory.edit', $product->Product_ID) }}"
                           class="text-yellow-600 hover:text-yellow-700 transition transform hover:scale-110" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 113.182 3.183L7.5 19.215 3 21l1.784-4.5 12.078-13.013z" />
                            </svg>
                        </a>
                        <form action="{{ route('inventory.destroy', $product->Product_ID) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700 transition transform hover:scale-110" title="Delete">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr id="noResults">
                    <td colspan="10" class="py-8 px-4 text-center text-gray-500">
                        No products available
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div id="paginationControls" class="px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6">
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span class="font-medium" id="pageStart">1</span> to <span class="font-medium" id="pageEnd">10</span> of <span class="font-medium" id="totalItems">0</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button id="prevBtn" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Previous</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <div id="numberedButtonsContainer" class="flex">
                            </div>

                        <button id="nextBtn" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </nav>
                </div>
            </div>
            <div class="flex-1 flex justify-between sm:hidden">
                <button id="mobilePrevBtn" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</button>
                <button id="mobileNextBtn" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('productsTableBody');
    const rows = Array.from(tableBody.querySelectorAll('tr.product-row')); 
    const noResults = document.getElementById('noResults');
    const numberedButtonsContainer = document.getElementById('numberedButtonsContainer');
    
    // Pagination Variables
    const itemsPerPage = 10;
    let currentPage = 1;
    let filteredRows = rows;

    const pageStart = document.getElementById('pageStart');
    const pageEnd = document.getElementById('pageEnd');
    const totalItems = document.getElementById('totalItems');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const mobilePrevBtn = document.getElementById('mobilePrevBtn');
    const mobileNextBtn = document.getElementById('mobileNextBtn');

    function updateTable() {
        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / itemsPerPage);

        // Validation
        if (currentPage < 1) currentPage = 1;
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        // Display Logic
        rows.forEach(row => row.style.display = 'none');

        if (totalRows > 0) {
            filteredRows.slice(start, end).forEach(row => row.style.display = '');
            noResults.style.display = 'none';
        } else {
            noResults.style.display = '';
            if (searchInput.value.trim() !== '') {
                noResults.querySelector('td').textContent = 'No products found matching "' + searchInput.value + '"';
            } else {
                noResults.querySelector('td').textContent = 'No products available';
            }
        }

        // Stats
        totalItems.textContent = totalRows;
        pageStart.textContent = totalRows === 0 ? 0 : start + 1;
        pageEnd.textContent = Math.min(end, totalRows);

        // Update Buttons (Enable/Disable)
        const isFirst = currentPage === 1;
        const isLast = currentPage === totalPages || totalPages === 0;

        prevBtn.disabled = isFirst;
        mobilePrevBtn.disabled = isFirst;
        nextBtn.disabled = isLast;
        mobileNextBtn.disabled = isLast;

        [prevBtn, nextBtn, mobilePrevBtn, mobileNextBtn].forEach(btn => {
            if (btn.disabled) {
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });

        // ---------------------------------------------------------
        //  DYNAMIC NUMBERED BUTTONS GENERATOR
        // ---------------------------------------------------------
        numberedButtonsContainer.innerHTML = ''; // Clear existing numbers

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            // Base classes for pagination numbers
            let classes = "relative inline-flex items-center px-4 py-2 border text-sm font-medium";
            
            if (i === currentPage) {
                // Active Page Style (Green Background)
                classes += " z-10 bg-green-50 border-green-500 text-green-600";
            } else {
                // Inactive Page Style (White Background)
                classes += " bg-white border-gray-300 text-gray-500 hover:bg-gray-50";
            }
            
            btn.className = classes;
            
            // Add Click Event
            btn.addEventListener('click', function() {
                currentPage = i;
                updateTable();
            });

            numberedButtonsContainer.appendChild(btn);
        }
    }

    // Navigation Events
    function changePage(delta) {
        currentPage += delta;
        updateTable();
    }

    prevBtn.addEventListener('click', () => changePage(-1));
    nextBtn.addEventListener('click', () => changePage(1));
    mobilePrevBtn.addEventListener('click', () => changePage(-1));
    mobileNextBtn.addEventListener('click', () => changePage(1));

    // Search Events
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        filteredRows = rows.filter(row => {
            const productName = row.getAttribute('data-product-name') || '';
            const category = row.getAttribute('data-category') || '';
            const variety = row.getAttribute('data-variety') || '';
            const supplier = row.getAttribute('data-supplier') || '';
            return productName.includes(searchTerm) || category.includes(searchTerm) || variety.includes(searchTerm) || supplier.includes(searchTerm);
        });
        currentPage = 1; // Reset to page 1 on search
        updateTable();
    });

    // Init
    updateTable();
});
</script>
@endsection