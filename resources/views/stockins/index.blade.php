@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 class="text-3xl font-extrabold text-green-700">Stock-In Management</h1>
        <div class="flex gap-3">
            <a href="{{ route('stockins.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-semibold shadow-md transition transform hover:scale-105">
                + Add Stock
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-3">
        <div class="flex gap-2">
            <div class="flex-1 max-w-md">
                <input type="text" 
                       id="searchInput"
                       placeholder="Search stock records..." 
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
                    <th class="py-3 px-4 text-left text-sm font-semibold">Product</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Variety</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Date</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Quantity</th>
                    <th class="py-3 px-4 text-right text-sm font-semibold">Price</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Unit</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Expiry Date</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Critical Level</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white" id="stocksTableBody">
                @forelse($stockIns as $stock)
                <tr class="hover:bg-green-50 transition stock-row" 
                    data-product-name="{{ strtolower($stock->product->Product_Name) }}" 
                    data-variety="{{ strtolower($stock->product->variety ?? '') }}"
                    data-unit="{{ strtolower($stock->unit) }}"
                    data-date="{{ $stock->date->format('Y-m-d') }}">
                    
                    <td class="py-2 px-4 text-sm font-medium text-gray-800">{{ $stock->product->Product_Name }}</td>
                    <td class="py-2 px-4 text-sm text-gray-700">
                        @if($stock->product->variety)
                            <span class="text-gray-700">{{ $stock->product->variety }}</span>
                        @else
                            <span class="text-gray-400 text-sm">N/A</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 text-center text-sm text-gray-700">{{ $stock->date->format('M d, Y') }}</td>
                    <td class="py-2 px-4 text-center">
                        <span class="font-semibold {{ $stock->quantity <= $stock->critical_level ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $stock->quantity }}
                        </span>
                    </td>
                    <td class="py-2 px-4 text-right text-sm text-gray-700">₱{{ number_format($stock->price, 2) }}</td>
                    <td class="py-2 px-4 text-center text-sm text-gray-700">{{ $stock->unit }}</td>
                    <td class="py-2 px-4 text-center text-sm">
                        @if($stock->expiry_date)
                            @php
                                $daysLeft = \Carbon\Carbon::now()->startOfDay()->diffInDays($stock->expiry_date->startOfDay(), false);
                                $isExpiringSoon = $daysLeft > 0 && $daysLeft <= 7;
                                $isExpired = $daysLeft <= 0;
                            @endphp
                            
                            @if($isExpired)
                                <span class="text-red-600 font-bold">Expired</span>
                            @elseif($isExpiringSoon)
                                <span class="text-orange-600 font-semibold">
                                    {{ $stock->expiry_date->format('M d, Y') }}
                                    <span class="block text-xs">({{ $daysLeft }} days)</span>
                                </span>
                            @else
                                <span class="text-gray-600">{{ $stock->expiry_date->format('M d, Y') }}</span>
                            @endif
                        @else
                            <span class="text-gray-400 text-sm">N/A</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 text-center text-sm text-gray-700">{{ $stock->critical_level }}</td>
                    
                    <td class="py-2 px-4">
                        <div class="flex justify-center gap-3">
                            <a href="{{ route('stockins.edit', $stock->Stock_ID) }}"
                                class="text-yellow-600 hover:text-yellow-700 transition transform hover:scale-110"
                                title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 113.182 3.183L7.5 19.215 3 21l1.784-4.5 12.078-13.013z" />
                                </svg>
                            </a>

                            <form action="{{ route('stockins.destroy', $stock->Stock_ID) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this stock record?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="text-red-600 hover:text-red-700 transition transform hover:scale-110"
                                    title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="noResults">
                    <td colspan="9" class="py-8 px-4 text-center text-gray-500">
                        No stock records found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div id="paginationControls" class="px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <button id="mobilePrevBtn" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</button>
                <button id="mobileNextBtn" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</button>
            </div>
            
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
                        
                        <div id="numberedButtonsContainer" class="flex"></div>

                        <button id="nextBtn" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('stocksTableBody');
    // Select the stock rows specifically
    const rows = Array.from(tableBody.querySelectorAll('tr.stock-row')); 
    const noResults = document.getElementById('noResults');
    const numberedButtonsContainer = document.getElementById('numberedButtonsContainer');
    
    // Pagination Config
    const itemsPerPage = 10;
    let currentPage = 1;
    let filteredRows = rows; // Initially all rows are visible

    // Elements
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

        // Sanity check current page
        if (currentPage < 1) currentPage = 1;
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        // Hide ALL rows initially
        rows.forEach(row => row.style.display = 'none');

        // Show only the visible rows for current page
        if (totalRows > 0) {
            filteredRows.slice(start, end).forEach(row => row.style.display = '');
            noResults.style.display = 'none';
        } else {
            noResults.style.display = '';
            if (searchInput.value.trim() !== '') {
                noResults.querySelector('td').textContent = 'No stock records found matching "' + searchInput.value + '"';
            } else {
                noResults.querySelector('td').textContent = 'No stock records found';
            }
        }

        // Update Text Stats
        totalItems.textContent = totalRows;
        pageStart.textContent = totalRows === 0 ? 0 : start + 1;
        pageEnd.textContent = Math.min(end, totalRows);

        // Button States (Disable if first/last page)
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

        // ---------------------------------------------
        // DYNAMIC NUMBERED BUTTONS GENERATION
        // ---------------------------------------------
        numberedButtonsContainer.innerHTML = ''; // Reset

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            let classes = "relative inline-flex items-center px-4 py-2 border text-sm font-medium";
            
            if (i === currentPage) {
                // Active Page (Green)
                classes += " z-10 bg-green-50 border-green-500 text-green-600";
            } else {
                // Inactive Page (White)
                classes += " bg-white border-gray-300 text-gray-500 hover:bg-gray-50";
            }
            
            btn.className = classes;
            btn.addEventListener('click', function() {
                currentPage = i;
                updateTable();
            });

            numberedButtonsContainer.appendChild(btn);
        }
    }

    // Prev/Next Logic
    function changePage(delta) {
        currentPage += delta;
        updateTable();
    }

    prevBtn.addEventListener('click', () => changePage(-1));
    nextBtn.addEventListener('click', () => changePage(1));
    mobilePrevBtn.addEventListener('click', () => changePage(-1));
    mobileNextBtn.addEventListener('click', () => changePage(1));

    // Search Logic
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        // Filter rows based on your criteria
        filteredRows = rows.filter(row => {
            const productName = row.getAttribute('data-product-name') || '';
            const variety = row.getAttribute('data-variety') || '';
            const unit = row.getAttribute('data-unit') || '';
            const date = row.getAttribute('data-date') || '';
            
            return productName.includes(searchTerm) || 
                   variety.includes(searchTerm) || 
                   unit.includes(searchTerm) ||
                   date.includes(searchTerm);
        });

        // Reset to Page 1 when searching
        currentPage = 1;
        updateTable();
    });

    // Initialize
    updateTable();
});
</script>
@endsection

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 class="text-3xl font-extrabold text-green-700">Stock-In Management</h1>
        <div class="flex gap-3">
            <a href="{{ route('stockins.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-semibold shadow-md transition transform hover:scale-105">
                + Add Stock
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-3">
        <div class="flex gap-2">
            <div class="flex-1 max-w-md">
                <input type="text" 
                       id="searchInput"
                       placeholder="Search stock records..." 
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
                    <th class="py-3 px-4 text-left text-sm font-semibold">Product</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Variety</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Date</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Quantity</th>
                    <th class="py-3 px-4 text-right text-sm font-semibold">Price</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Unit</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Expiry Date</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Critical Level</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white" id="stocksTableBody">
                @forelse($stockIns as $stock)
                <tr class="hover:bg-green-50 transition stock-row" 
                    data-product-name="{{ strtolower($stock->product->Product_Name) }}" 
                    data-variety="{{ strtolower($stock->product->variety ?? '') }}"
                    data-unit="{{ strtolower($stock->unit) }}"
                    data-date="{{ $stock->date->format('Y-m-d') }}">
                    
                    <td class="py-2 px-4 text-sm font-medium text-gray-800">{{ $stock->product->Product_Name }}</td>
                    <td class="py-2 px-4 text-sm text-gray-700">
                        @if($stock->product->variety)
                            <span class="text-gray-700">{{ $stock->product->variety }}</span>
                        @else
                            <span class="text-gray-400 text-sm">N/A</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 text-center text-sm text-gray-700">{{ $stock->date->format('M d, Y') }}</td>
                    <td class="py-2 px-4 text-center">
                        <span class="font-semibold {{ $stock->quantity <= $stock->critical_level ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $stock->quantity }}
                        </span>
                    </td>
                    <td class="py-2 px-4 text-right text-sm text-gray-700">₱{{ number_format($stock->price, 2) }}</td>
                    <td class="py-2 px-4 text-center text-sm text-gray-700">{{ $stock->unit }}</td>
                    <td class="py-2 px-4 text-center text-sm">
                        @if($stock->expiry_date)
                            @php
                                $daysLeft = \Carbon\Carbon::now()->startOfDay()->diffInDays($stock->expiry_date->startOfDay(), false);
                                $isExpiringSoon = $daysLeft > 0 && $daysLeft <= 7;
                                $isExpired = $daysLeft <= 0;
                            @endphp
                            
                            @if($isExpired)
                                <span class="text-red-600 font-bold">Expired</span>
                            @elseif($isExpiringSoon)
                                <span class="text-orange-600 font-semibold">
                                    {{ $stock->expiry_date->format('M d, Y') }}
                                    <span class="block text-xs">({{ $daysLeft }} days)</span>
                                </span>
                            @else
                                <span class="text-gray-600">{{ $stock->expiry_date->format('M d, Y') }}</span>
                            @endif
                        @else
                            <span class="text-gray-400 text-sm">N/A</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 text-center text-sm text-gray-700">{{ $stock->critical_level }}</td>
                    
                    <td class="py-2 px-4">
                        <div class="flex justify-center gap-3">
                            <a href="{{ route('stockins.edit', $stock->Stock_ID) }}"
                                class="text-yellow-600 hover:text-yellow-700 transition transform hover:scale-110"
                                title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 113.182 3.183L7.5 19.215 3 21l1.784-4.5 12.078-13.013z" />
                                </svg>
                            </a>

                            <form action="{{ route('stockins.destroy', $stock->Stock_ID) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this stock record?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="text-red-600 hover:text-red-700 transition transform hover:scale-110"
                                    title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="noResults">
                    <td colspan="9" class="py-8 px-4 text-center text-gray-500">
                        No stock records found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div id="paginationControls" class="px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                <button id="mobilePrevBtn" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</button>
                <button id="mobileNextBtn" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</button>
            </div>
            
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
                        
                        <div id="numberedButtonsContainer" class="flex"></div>

                        <button id="nextBtn" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Next</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('stocksTableBody');
    // Select the stock rows specifically
    const rows = Array.from(tableBody.querySelectorAll('tr.stock-row')); 
    const noResults = document.getElementById('noResults');
    const numberedButtonsContainer = document.getElementById('numberedButtonsContainer');
    
    // Pagination Config
    const itemsPerPage = 10;
    let currentPage = 1;
    let filteredRows = rows; // Initially all rows are visible

    // Elements
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

        // Sanity check current page
        if (currentPage < 1) currentPage = 1;
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        // Hide ALL rows initially
        rows.forEach(row => row.style.display = 'none');

        // Show only the visible rows for current page
        if (totalRows > 0) {
            filteredRows.slice(start, end).forEach(row => row.style.display = '');
            noResults.style.display = 'none';
        } else {
            noResults.style.display = '';
            if (searchInput.value.trim() !== '') {
                noResults.querySelector('td').textContent = 'No stock records found matching "' + searchInput.value + '"';
            } else {
                noResults.querySelector('td').textContent = 'No stock records found';
            }
        }

        // Update Text Stats
        totalItems.textContent = totalRows;
        pageStart.textContent = totalRows === 0 ? 0 : start + 1;
        pageEnd.textContent = Math.min(end, totalRows);

        // Button States (Disable if first/last page)
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

        // ---------------------------------------------
        // DYNAMIC NUMBERED BUTTONS GENERATION
        // ---------------------------------------------
        numberedButtonsContainer.innerHTML = ''; // Reset

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            let classes = "relative inline-flex items-center px-4 py-2 border text-sm font-medium";
            
            if (i === currentPage) {
                // Active Page (Green)
                classes += " z-10 bg-green-50 border-green-500 text-green-600";
            } else {
                // Inactive Page (White)
                classes += " bg-white border-gray-300 text-gray-500 hover:bg-gray-50";
            }
            
            btn.className = classes;
            btn.addEventListener('click', function() {
                currentPage = i;
                updateTable();
            });

            numberedButtonsContainer.appendChild(btn);
        }
    }

    // Prev/Next Logic
    function changePage(delta) {
        currentPage += delta;
        updateTable();
    }

    prevBtn.addEventListener('click', () => changePage(-1));
    nextBtn.addEventListener('click', () => changePage(1));
    mobilePrevBtn.addEventListener('click', () => changePage(-1));
    mobileNextBtn.addEventListener('click', () => changePage(1));

    // Search Logic
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        // Filter rows based on your criteria
        filteredRows = rows.filter(row => {
            const productName = row.getAttribute('data-product-name') || '';
            const variety = row.getAttribute('data-variety') || '';
            const unit = row.getAttribute('data-unit') || '';
            const date = row.getAttribute('data-date') || '';
            
            return productName.includes(searchTerm) || 
                   variety.includes(searchTerm) || 
                   unit.includes(searchTerm) ||
                   date.includes(searchTerm);
        });

        // Reset to Page 1 when searching
        currentPage = 1;
        updateTable();
    });

    // Initialize
    updateTable();
});
</script>
@endsection