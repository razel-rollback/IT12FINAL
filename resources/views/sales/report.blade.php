@extends('layouts.app')

@section('page_title', 'Sales Report & Analytics')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-1">Sales Analytics</h1>
                    <p class="text-sm text-gray-500">Track and analyze your sales performance</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200 border-l-4 border-l-green-500">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-medium text-gray-600 uppercase tracking-wide">Total Sales</h3>
                </div>
                <div class="space-y-1">
                    <p class="text-2xl font-semibold text-gray-900 text-right">₱{{ number_format($totalSales, 2) }}</p>
                    <p class="text-xs text-gray-500 text-right">{{ $analytics['total_transactions'] }} transactions</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200 border-l-4 border-l-blue-500">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-medium text-gray-600 uppercase tracking-wide">Today's Sales</h3>
                </div>
                <div class="space-y-1">
                    <p class="text-2xl font-semibold text-gray-900 text-right">₱{{ number_format($todaySales, 2) }}</p>
                    <p class="text-xs text-gray-500 text-right">{{ $analytics['today_transactions'] }} transactions</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200 border-l-4 border-l-purple-500">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-medium text-gray-600 uppercase tracking-wide">This Week</h3>
                </div>
                <div class="space-y-1">
                    <p class="text-2xl font-semibold text-gray-900 text-right">₱{{ number_format($weeklySales, 2) }}</p>
                    <p class="text-xs text-gray-500 text-right">Week to date</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200 border-l-4 border-l-amber-500">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-medium text-gray-600 uppercase tracking-wide">This Month</h3>
                </div>
                <div class="space-y-1">
                    <p class="text-2xl font-semibold text-gray-900 text-right">₱{{ number_format($monthlySales, 2) }}</p>
                    <p class="text-xs text-gray-500 text-right">
                        @if($analytics['growth_percentage'] >= 0)
                            <span class="text-green-600 font-medium">↑ {{ number_format($analytics['growth_percentage'], 1) }}%</span>
                        @else
                            <span class="text-red-600 font-medium">↓ {{ number_format(abs($analytics['growth_percentage']), 1) }}%</span>
                        @endif
                        <span>vs last month</span>
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200 border-l-4 border-l-orange-500">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-medium text-gray-600 uppercase tracking-wide">This Year</h3>
                </div>
                <div class="space-y-1">
                    <p class="text-2xl font-semibold text-gray-900 text-right">₱{{ number_format($yearlySales, 2) }}</p>
                    <p class="text-xs text-gray-500 text-right">Year to date</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-wallet text-gray-400 mr-2"></i>
                    Payment Methods
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-money-bill-wave text-green-600 mr-3"></i>
                            <span class="text-sm font-medium text-gray-700">Cash</span>
                        </div>
                        <span class="text-lg font-bold text-green-600">₱{{ number_format($analytics['cash_total'], 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-mobile-alt text-blue-600 mr-3"></i>
                            <span class="text-sm font-medium text-gray-700">GCash</span>
                        </div>
                        <span class="text-lg font-bold text-blue-600">₱{{ number_format($analytics['gcash_total'], 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-tasks text-gray-400 mr-2"></i>
                    Transaction Status
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3"></i>
                            <span class="text-sm font-medium text-gray-700">Paid</span>
                        </div>
                        <span class="text-lg font-bold text-green-600">{{ $analytics['paid_transactions'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-amber-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-amber-600 mr-3"></i>
                            <span class="text-sm font-medium text-gray-700">Pending</span>
                        </div>
                        <span class="text-lg font-bold text-amber-600">{{ $analytics['pending_transactions'] }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-gray-400 mr-2"></i>
                    Performance
                </h3>
                <div class="space-y-4">
                    <div class="p-3 bg-purple-50 rounded-lg">
                        <div class="flex items-center mb-1">
                            <i class="fas fa-receipt text-purple-600 mr-2 text-sm"></i>
                            <span class="text-xs font-medium text-gray-600">Avg Transaction</span>
                        </div>
                        <span class="text-lg font-bold text-purple-600">₱{{ number_format($analytics['avg_transaction_value'], 2) }}</span>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-lg">
                        <div class="flex items-center mb-1">
                            <i class="fas fa-user-circle text-indigo-600 mr-2 text-sm"></i>
                            <span class="text-xs font-medium text-gray-600">Top Customer</span>
                        </div>
                        <span class="text-sm font-bold text-indigo-600 truncate block">
                            @if($analytics['top_customer'] && $analytics['top_customer']->customer)
                                {{ $analytics['top_customer']->customer->Customer_Name }}
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-trophy text-gray-400 mr-2"></i>
                Top 5 Products
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach($topProducts as $index => $product)
                    <div class="relative bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-xl p-5 hover:shadow-md transition-all duration-200">
                        <div class="absolute -top-3 -left-3 w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                            {{ $index + 1 }}
                        </div>
                        <div class="mt-3">
                            <h4 class="text-sm font-semibold text-gray-900 mb-2 line-clamp-2 min-h-[2.5rem]">
                                {{ $product->product->Product_Name ?? 'N/A' }}
                            </h4>
                            <div class="flex items-center text-xs text-gray-600 mb-2">
                                <i class="fas fa-box mr-1"></i>
                                <span>Qty: {{ number_format($product->total_quantity, 2) }}</span>
                            </div>
                            <div class="text-lg font-bold text-green-600">
                                ₱{{ number_format($product->total_revenue, 2) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Search Bar -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
            <div class="flex gap-2">
                <div class="flex-1 max-w-md">
                    <input type="text" 
                           id="searchInput"
                           placeholder="Search by receipt, customer, product, payment, or status..." 
                           class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <div class="flex justify-end mb-4">
            <button onclick="printSalesHistory()" 
                    class="inline-flex items-center px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 shadow-sm">
                <i class="fas fa-print mr-2"></i>
                Print Sales History
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Receipt #</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Products</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white" id="reportTableBody">
                        {{-- SORTED NEWEST TO OLDEST BY DATE --}}
                        @forelse($sales->sortByDesc('transaction_date') as $sale)
                            <tr class="hover:bg-gray-50 transition-colors duration-150 report-row"
                                data-customer="{{ strtolower($sale->customer ? $sale->customer->Customer_Name : 'walk-in customer') }}"
                                data-receipt="{{ strtolower($sale->receipt_number) }}"
                                data-payment="{{ strtolower($sale->payment_method) }}"
                                data-status="{{ strtolower($sale->status) }}"
                                data-products="{{ strtolower($sale->details->pluck('product.Product_Name')->implode(' ')) }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">{{ $sale->receipt_number }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($sale->transaction_date)->format('M d, Y') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($sale->customer)
                                        <span class="text-sm font-medium text-gray-900">{{ $sale->customer->Customer_Name }}</span>
                                    @else
                                        <div class="flex items-center">
                                            <i class="fas fa-walking text-blue-500 mr-2"></i>
                                            <span class="text-sm text-gray-500 italic">Walk-in Customer</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        @if($sale->details && $sale->details->count() > 0)
                                            @foreach($sale->details->take(2) as $detail)
                                                <div class="flex items-center gap-1 text-xs mb-1">
                                                    <span class="text-green-600">•</span>
                                                    <span class="text-gray-700">{{ $detail->product->Product_Name }}</span>
                                                    <span class="text-gray-500">({{ $detail->Quantity }}{{ $detail->product->variety ? ' - ' . $detail->product->variety : '' }})</span>
                                                </div>
                                            @endforeach
                                            @if($sale->details->count() > 2)
                                                <div class="text-xs text-blue-600 font-medium">
                                                    +{{ $sale->details->count() - 2 }} more
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-gray-400 text-xs italic">No products</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600">{{ $sale->payment_method }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($sale->status == 'Paid')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $sale->status }}</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">{{ $sale->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                    ₱{{ number_format($sale->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                    <a href="{{ route('sales.printReceipt', $sale->transaction_ID) }}" 
                                       class="text-green-600 hover:text-green-800 transition-colors" 
                                       target="_blank"
                                       title="Print Receipt">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr id="noResults">
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No sales found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('reportTableBody');
    const rows = Array.from(tableBody.querySelectorAll('tr.report-row')); 
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

    // Make updateTable global so print function can use it
    window.updateTable = function() {
        const totalRows = filteredRows.length;
        const totalPages = Math.ceil(totalRows / itemsPerPage);

        if (currentPage < 1) currentPage = 1;
        if (currentPage > totalPages && totalPages > 0) currentPage = totalPages;

        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        rows.forEach(row => row.style.display = 'none');

        if (totalRows > 0) {
            filteredRows.slice(start, end).forEach(row => row.style.display = '');
            if(noResults) noResults.style.display = 'none';
        } else {
            if(noResults) {
                noResults.style.display = '';
                if (searchInput.value.trim() !== '') {
                    noResults.querySelector('td').textContent = 'No sales found matching "' + searchInput.value + '"';
                } else {
                    noResults.querySelector('td').textContent = 'No sales found';
                }
            }
        }

        totalItems.textContent = totalRows;
        pageStart.textContent = totalRows === 0 ? 0 : start + 1;
        pageEnd.textContent = Math.min(end, totalRows);

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

        numberedButtonsContainer.innerHTML = ''; 

        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            let classes = "relative inline-flex items-center px-4 py-2 border text-sm font-medium";
            
            if (i === currentPage) {
                classes += " z-10 bg-blue-50 border-blue-500 text-blue-600";
            } else {
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

    function changePage(delta) {
        currentPage += delta;
        updateTable();
    }

    prevBtn.addEventListener('click', () => changePage(-1));
    nextBtn.addEventListener('click', () => changePage(1));
    mobilePrevBtn.addEventListener('click', () => changePage(-1));
    mobileNextBtn.addEventListener('click', () => changePage(1));

    // Search Logic - Now includes product search
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        // Filter rows based on criteria
        filteredRows = rows.filter(row => {
            const customer = row.getAttribute('data-customer') || '';
            const receipt = row.getAttribute('data-receipt') || '';
            const payment = row.getAttribute('data-payment') || '';
            const status = row.getAttribute('data-status') || '';
            const products = row.getAttribute('data-products') || '';
            
            return customer.includes(searchTerm) || 
                   receipt.includes(searchTerm) ||
                   payment.includes(searchTerm) ||
                   status.includes(searchTerm) ||
                   products.includes(searchTerm);
        });

        // Reset to Page 1 when searching
        currentPage = 1;
        updateTable();
    });

    // Init
    updateTable();
});

// UPDATED PRINT FUNCTION WITH COMPANY NAME
function printSalesHistory() {
    // Show all rows before printing to print complete history
    const allRows = document.querySelectorAll('tr.report-row');
    allRows.forEach(row => row.style.display = '');
    
    // Hide controls
    document.getElementById('paginationControls').style.display = 'none';

    const tableContent = document.querySelector('.overflow-x-auto').innerHTML;
    const newWindow = window.open('', '', 'width=900,height=700');
    newWindow.document.write(`
        <html>
        <head>
            <title>Sales History - CRM / DONDON FRUITSTAND</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ccc; padding: 8px; text-align: left; font-size: 12px; }
                th { background-color: #f5f5f5; }
                h1 { text-align: center; margin-bottom: 4px; font-size: 20px; }
                h2 { text-align: center; margin-top: 0; margin-bottom: 16px; font-size: 16px; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .italic { font-style: italic; }
                .meta { margin-bottom: 16px; font-size: 13px; }
            </style>
        </head>
        <body>
            <!-- Company Header -->
            <h1>CRM / DONDON FRUITSTAND</h1>
            <h2>Sales History Report</h2>

            <div class="meta">
                <p>Generated on: ${new Date().toLocaleDateString()}</p>
            </div>

            <!-- Printed Table -->
            <table>${tableContent}</table>
        </body>
        </html>
    `);
    newWindow.document.close();
    newWindow.focus();
    newWindow.print();
    newWindow.close();

    // Restore Pagination State
    document.getElementById('paginationControls').style.display = '';
    window.updateTable(); // Call the global function to reset view
}
</script>
@endsection