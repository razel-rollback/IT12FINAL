@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 class="text-3xl font-extrabold text-gray-800">Supplier Transactions</h1>
        <div class="flex gap-3">
            <a href="{{ route('supplier-transactions.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold shadow-md transition transform hover:scale-105">
                + Add Transaction
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-3">
        <div class="flex gap-2">
            <div class="flex-1 max-w-md">
                <input type="text" 
                       id="searchInput"
                       placeholder="Search transactions..." 
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
            <thead class="bg-gray-100 rounded-t-xl">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Supplier</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Product</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Price/Kg</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Qty (Units)</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Qty (Kilos)</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Total Kg</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Supply Date</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Total Cost</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white" id="transactionsTableBody">
                @forelse($transactions as $t)
                    @php
                        // Calculate total kilos (units * unit_weight + kilos)
                        $unitWeight = $t->product->unit_weight ?? 0;
                        $totalKilos = $t->quantity_kilos + ($t->quantity_units * $unitWeight);
                        
                        // Recalculate total cost to ensure consistency
                        $calculatedTotal = $totalKilos * $t->supplier_price;
                    @endphp
                    
                    <tr class="hover:bg-gray-50 transition transaction-row"
                        data-supplier="{{ strtolower($t->supplier->Supplier_Name) }}"
                        data-product="{{ strtolower($t->product->Product_Name) }}"
                        data-status="{{ strtolower($t->status) }}"
                        data-date="{{ $t->supply_date }}">
                        
                        <td class="py-3 px-4 text-sm text-gray-800">{{ $t->supplier->Supplier_Name }}</td>
                        <td class="py-3 px-4 text-sm text-gray-800">{{ $t->product->Product_Name }}</td>
                        <td class="py-3 px-4 text-sm text-blue-700 font-semibold">₱{{ number_format($t->supplier_price, 2) }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ $t->quantity_units }}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ number_format($t->quantity_kilos, 2) }}</td>
                        <td class="py-3 px-4 text-sm font-medium text-gray-800">{{ number_format($totalKilos, 2) }} kg</td>
                        <td class="py-3 px-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($t->supply_date)->format('M d, Y') }}</td>
                        <td class="py-3 px-4 text-sm font-bold text-green-700">₱{{ number_format($calculatedTotal, 2) }}</td>

                        <td class="py-3 px-4">
                            @php
                                $statusColors = [
                                    'pending' => 'text-yellow-800 bg-yellow-200',
                                    'completed' => 'text-green-800 bg-green-200',
                                    'cancelled' => 'text-red-800 bg-red-200',
                                    'paid' => 'text-blue-800 bg-blue-200',
                                ];
                            @endphp

                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$t->status] ?? '' }}">
                                {{ ucfirst($t->status) }}
                            </span>
                        </td>

                        <td class="py-3 px-4">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('supplier-transactions.edit', $t->Supply_transac_ID) }}"
                                   class="text-blue-600 hover:text-blue-800 transition transform hover:scale-110"
                                   title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 113.182 3.183L7.5 19.215 3 21l1.784-4.5 12.078-13.013z" />
                                    </svg>
                                </a>

                                <form action="{{ route('supplier-transactions.destroy', $t->Supply_transac_ID) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this transaction?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-800 transition transform hover:scale-110"
                                            title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>

                                @if($t->status == 'pending')
                                    <form action="{{ route('supplier-transactions.pay', $t->Supply_transac_ID) }}"
                                          method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button class="text-green-600 hover:text-green-800 transition transform hover:scale-110"
                                                title="Mark as Paid">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('supplier-transactions.receipt', $t->Supply_transac_ID) }}"
                                   class="text-purple-600 hover:text-purple-800 transition transform hover:scale-110"
                                   title="View Receipt">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>

                @empty
                    <tr id="noResults">
                        <td colspan="10" class="py-8 px-4 text-center text-gray-500">
                            No transactions found.
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
    const tableBody = document.getElementById('transactionsTableBody');
    // Select the transaction rows specifically
    const rows = Array.from(tableBody.querySelectorAll('tr.transaction-row')); 
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

        // Sanity check
        if (currentPage < 1) currentPage = 1;
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        // Hide ALL rows first
        rows.forEach(row => row.style.display = 'none');

        // Show only visible rows for current page
        if (totalRows > 0) {
            filteredRows.slice(start, end).forEach(row => row.style.display = '');
            noResults.style.display = 'none';
        } else {
            noResults.style.display = '';
            if (searchInput.value.trim() !== '') {
                noResults.querySelector('td').textContent = 'No transactions found matching "' + searchInput.value + '"';
            } else {
                noResults.querySelector('td').textContent = 'No transactions found.';
            }
        }

        // Update Text Stats
        totalItems.textContent = totalRows;
        pageStart.textContent = totalRows === 0 ? 0 : start + 1;
        pageEnd.textContent = Math.min(end, totalRows);

        // Button States
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
                // Active Page (Blue for Transactions Page)
                classes += " z-10 bg-blue-50 border-blue-500 text-blue-600";
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
        
        // Filter rows based on criteria
        filteredRows = rows.filter(row => {
            const supplier = row.getAttribute('data-supplier') || '';
            const product = row.getAttribute('data-product') || '';
            const status = row.getAttribute('data-status') || '';
            const date = row.getAttribute('data-date') || '';
            
            return supplier.includes(searchTerm) || 
                   product.includes(searchTerm) || 
                   status.includes(searchTerm) ||
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