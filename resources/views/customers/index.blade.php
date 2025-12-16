@extends('layouts.app') 

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 class="text-3xl font-extrabold text-green-700">Customers</h1>
        <div class="flex gap-3">
            <a href="{{ route('customers.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-semibold shadow-md transition transform hover:scale-105">
                + Add Customer
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-3">
        <div class="flex gap-2">
            <div class="flex-1 max-w-md">
                <input type="text" 
                       id="searchInput"
                       placeholder="Search customers..." 
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
                    <th class="py-3 px-4 text-left text-sm font-semibold">Customer Name</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Contact Number</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Receipt</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Total</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Status</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white" id="customersTableBody">
                @foreach($customers as $customer)
                    @if($customer->sales && $customer->sales->count() > 0)
                        @foreach($customer->sales as $sale)
                            <tr class="hover:bg-green-50 transition customer-row"
                                data-customer-name="{{ strtolower($customer->Customer_Name) }}"
                                data-contact-number="{{ $customer->Contact_Number }}"
                                data-receipt="{{ strtolower($sale->receipt_number) }}"
                                data-status="{{ strtolower($sale->status) }}">
                                
                                <td class="py-3 px-4 text-sm text-gray-800">{{ $customer->Customer_Name }}</td>
                                <td class="py-3 px-4 text-sm text-gray-700">{{ $customer->Contact_Number }}</td>
                                <td class="py-3 px-4 text-sm text-gray-700">{{ $sale->receipt_number }}</td>
                                <td class="py-3 px-4 text-sm font-semibold text-gray-800">₱{{ number_format($sale->total_amount, 2) }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $sale->status == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 flex gap-3">
                                    <a href="{{ route('customers.show', $customer->Customer_ID) }}" 
                                       class="text-blue-600 hover:text-blue-700 transition transform hover:scale-110"
                                       title="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    <a href="{{ route('customers.edit', $customer->Customer_ID) }}" 
                                       class="text-yellow-600 hover:text-yellow-700 transition transform hover:scale-110"
                                       title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 113.182 3.183L7.5 19.215 3 21l1.784-4.5 12.078-13.013z" />
                                        </svg>
                                    </a>

                                    <form action="{{ route('customers.destroy', $customer->Customer_ID) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to archive this customer? You can restore it later from the archive.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-orange-600 hover:text-orange-700 transition transform hover:scale-110"
                                                title="Archive">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="hover:bg-green-50 transition customer-row"
                            data-customer-name="{{ strtolower($customer->Customer_Name) }}"
                            data-contact-number="{{ $customer->Contact_Number }}"
                            data-receipt=""
                            data-status="no-purchase">
                            
                            <td class="py-3 px-4 text-sm text-gray-800">{{ $customer->Customer_Name }}</td>
                            <td class="py-3 px-4 text-sm text-gray-700">{{ $customer->Contact_Number }}</td>
                            <td class="py-3 px-4 text-sm text-gray-400 italic" colspan="3">No purchases yet</td>
                            <td class="py-3 px-4 flex gap-3">
                                <a href="{{ route('customers.show', $customer->Customer_ID) }}" 
                                   class="text-blue-600 hover:text-blue-700 transition transform hover:scale-110"
                                   title="View">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                                <a href="{{ route('customers.edit', $customer->Customer_ID) }}" 
                                   class="text-yellow-600 hover:text-yellow-700 transition transform hover:scale-110"
                                   title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 113.182 3.183L7.5 19.215 3 21l1.784-4.5 12.078-13.013z" />
                                    </svg>
                                </a>

                                <form action="{{ route('customers.destroy', $customer->Customer_ID) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to archive this customer? You can restore it later from the archive.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-orange-600 hover:text-orange-700 transition transform hover:scale-110"
                                            title="Archive">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endif
                @endforeach
                
                <tr id="noResults" style="display: none;">
                    <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                        No customers found
                    </td>
                </tr>
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
    const tableBody = document.getElementById('customersTableBody');
    // Targeting all rows with the class 'customer-row'
    const rows = Array.from(tableBody.querySelectorAll('tr.customer-row')); 
    const noResults = document.getElementById('noResults');
    const numberedButtonsContainer = document.getElementById('numberedButtonsContainer');
    
    // Pagination Config
    const itemsPerPage = 10;
    let currentPage = 1;
    let filteredRows = rows; // Initially, all rows are visible

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

        // Sanity Check
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
                noResults.querySelector('td').textContent = 'No customers found matching "' + searchInput.value + '"';
            } else {
                noResults.querySelector('td').textContent = 'No customers found';
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
                // Active Page (Green for Customers Page)
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
        
        // Filter rows based on criteria
        filteredRows = rows.filter(row => {
            const customerName = row.getAttribute('data-customer-name') || '';
            const contactNumber = row.getAttribute('data-contact-number') || '';
            const receipt = row.getAttribute('data-receipt') || '';
            const status = row.getAttribute('data-status') || '';
            
            return customerName.includes(searchTerm) || 
                   contactNumber.includes(searchTerm) || 
                   receipt.includes(searchTerm) ||
                   status.includes(searchTerm);
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