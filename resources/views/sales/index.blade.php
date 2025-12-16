@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 class="text-3xl font-extrabold text-green-700">Sales Transactions</h1>
        <div class="flex gap-3">
            <a href="{{ route('sales.walkIn.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold shadow-md transition transform hover:scale-105">
                🚶 Walk-in Sale
            </a>
            <a href="{{ route('sales.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-semibold shadow-md transition transform hover:scale-105">
                + Add Sale (With Customer)
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-3">
        <div class="flex gap-2">
            <div class="flex-1 max-w-md">
                <input type="text" 
                       id="searchInput"
                       placeholder="Search transactions..." 
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md animate-fade">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-md animate-fade">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-green-700 text-white rounded-t-xl">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Customer</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Cashier</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Date</th>
                    <th class="py-3 px-4 text-right text-sm font-semibold">Total Amount</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Payment</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Status</th>
                    <th class="py-3 px-4 text-center text-sm font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white" id="salesTableBody">
                @forelse($transactions as $transaction)
                <tr class="hover:bg-green-50 transition transaction-row"
                    data-customer="{{ strtolower($transaction->customer ? $transaction->customer->Customer_Name : 'walk-in customer') }}"
                    data-cashier="{{ strtolower(($transaction->user ? $transaction->user->fname . ' ' . $transaction->user->lname : '')) }}"
                    data-payment="{{ strtolower($transaction->payment_method) }}"
                    data-status="{{ strtolower($transaction->status) }}"
                    data-date="{{ $transaction->transaction_date }}">
                    
                    <td class="py-3 px-4 text-sm text-gray-800">
                        @if($transaction->customer)
                            {{ $transaction->customer->Customer_Name }}
                        @else
                            <span class="text-gray-500 italic">Walk-in Customer</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-700">{{ $transaction->user ? ($transaction->user->fname . ' ' . $transaction->user->lname) : 'N/A' }}</td>
                    <td class="py-3 px-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                    <td class="py-3 px-4 text-right text-sm font-semibold text-gray-800">₱{{ number_format($transaction->total_amount, 2) }}</td>
                    <td class="py-3 px-4 text-center">
                        <span class="text-sm text-gray-600">{{ $transaction->payment_method }}</span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $transaction->status=='paid'?'bg-green-100 text-green-700':'bg-yellow-100 text-yellow-700' }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex gap-2 items-center justify-center">
                            <button onclick="viewTransaction({{ $transaction->transaction_ID }})" 
                                    class="text-blue-600 hover:text-blue-700 transition transform hover:scale-110" 
                                    title="View Details">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>

                            @if($transaction->status == 'pending')
                            <form action="{{ route('sales.markPaid', $transaction->transaction_ID) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" 
                                        class="text-green-600 hover:text-green-700 transition transform hover:scale-110 text-xs px-2 py-1"
                                        title="Mark as Paid">
                                    Mark Paid
                                </button>
                            </form>
                            @endif

                            <a href="{{ route('sales.edit', $transaction->transaction_ID) }}" 
                               class="text-yellow-600 hover:text-yellow-700 transition transform hover:scale-110" 
                               title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 113.182 3.183L7.5 19.215 3 21l1.784-4.5 12.078-13.013z" />
                                </svg>
                            </a>

                            <form action="{{ route('sales.destroy', $transaction->transaction_ID) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-700 transition transform hover:scale-110" 
                                        onclick="return confirm('Are you sure?')" 
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
                    <td colspan="7" class="py-8 px-4 text-center text-gray-500">No transactions found</td>
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

<div id="transactionModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="bg-green-700 text-white px-6 py-4 rounded-t-lg flex justify-between items-center">
            <h2 class="text-2xl font-bold">Transaction Details</h2>
            <button onclick="closeModal()" class="text-white hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="modalContent" class="p-6">
            </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('salesTableBody');
    // Select the specific rows by class
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
            if(noResults) noResults.style.display = 'none';
        } else {
            if(noResults) {
                noResults.style.display = '';
                if (searchInput.value.trim() !== '') {
                    noResults.querySelector('td').textContent = 'No transactions found matching "' + searchInput.value + '"';
                } else {
                    noResults.querySelector('td').textContent = 'No transactions found';
                }
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
                // Active Page (Green for Sales Page)
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
            const customer = row.getAttribute('data-customer') || '';
            const cashier = row.getAttribute('data-cashier') || '';
            const payment = row.getAttribute('data-payment') || '';
            const status = row.getAttribute('data-status') || '';
            const date = row.getAttribute('data-date') || '';
            
            return customer.includes(searchTerm) || 
                   cashier.includes(searchTerm) || 
                   payment.includes(searchTerm) ||
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

// Modal Functions (kept global as per original code structure)
function viewTransaction(transactionId) {
    document.getElementById('transactionModal').classList.remove('hidden');
    
    fetch(`/sales/${transactionId}/details`)
        .then(response => response.json())
        .then(data => {
            const customerName = data.customer.Customer_Name !== 'N/A' ? data.customer.Customer_Name : '<span class="text-gray-500 italic">Walk-in Customer</span>';
            
            const content = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded">
                        <div>
                            <p class="text-sm text-gray-600">Transaction ID</p>
                            <p class="font-semibold">#${data.transaction_ID}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Receipt Number</p>
                            <p class="font-semibold">${data.receipt_number}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Customer</p>
                            <p class="font-semibold">${customerName}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Cashier</p>
                            <p class="font-semibold">${data.user.fname} ${data.user.lname}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Date</p>
                            <p class="font-semibold">${new Date(data.transaction_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Payment Method</p>
                            <p class="font-semibold">${data.payment_method}</p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-3">Products</h3>
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-3 text-left text-sm">Product</th>
                                    <th class="py-2 px-3 text-center text-sm">Qty</th>
                                    <th class="py-2 px-3 text-right text-sm">Unit Price</th>
                                    <th class="py-2 px-3 text-right text-sm">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.details.map(detail => `
                                    <tr class="border-t">
                                        <td class="py-2 px-3">${detail.product.Product_Name}</td>
                                        <td class="py-2 px-3 text-center">${detail.Quantity}</td>
                                        <td class="py-2 px-3 text-right">₱${parseFloat(detail.unit_price).toFixed(2)}</td>
                                        <td class="py-2 px-3 text-right font-semibold">₱${(detail.Quantity * detail.unit_price).toFixed(2)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr class="border-t-2 border-gray-300">
                                    <td colspan="3" class="py-3 px-3 text-right font-bold text-lg">Total Amount:</td>
                                    <td class="py-3 px-3 text-right font-bold text-lg text-green-700">₱${parseFloat(data.total_amount).toFixed(2)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="flex justify-end gap-2 pt-4 border-t">
                        <button onclick="closeModal()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                            Close
                        </button>
                    </div>
                </div>
            `;
            document.getElementById('modalContent').innerHTML = content;
        })
        .catch(error => {
            document.getElementById('modalContent').innerHTML = `
                <div class="text-red-600 text-center py-8">
                    <p>Error loading transaction details.</p>
                    <button onclick="closeModal()" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Close</button>
                </div>
            `;
        });
}

function closeModal() {
    document.getElementById('transactionModal').classList.add('hidden');
}

document.getElementById('transactionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endsection