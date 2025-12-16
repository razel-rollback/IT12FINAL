@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-green-700">Customer Archive</h1>
            <p class="text-gray-600 mt-1">View and manage archived customers</p>
        </div>
        <a href="{{ route('customers.index') }}" 
           class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg font-semibold shadow-md transition transform hover:scale-105">
            ← Back to Customers
        </a>
    </div>

    <!-- Search Bar -->
    @if(!$customers->isEmpty())
    <div class="bg-white rounded-lg shadow-md p-3">
        <div class="flex gap-2">
            <div class="flex-1 max-w-md">
                <input type="text" 
                       id="searchInput"
                       placeholder="Search archived customers..." 
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
        </div>
    </div>
    @endif

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md animate-fade">
        {{ session('success') }}
    </div>
    @endif

    <!-- Archived Customers Table -->
    <div class="overflow-x-auto bg-white rounded-2xl shadow-lg">
        @if($customers->isEmpty())
            <div class="p-8 text-center text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto mb-4 text-gray-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
                <p class="text-lg font-medium">No archived customers</p>
                <p class="text-sm">Deleted customers will appear here</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-700 text-white rounded-t-xl">
                    <tr>
                        <th class="py-3 px-4 text-left text-sm font-semibold">Customer Name</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold">Contact Number</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold">Address</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold">Deleted At</th>
                        <th class="py-3 px-4 text-left text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white" id="customersTableBody">
                    @foreach($customers as $customer)
                    <tr class="hover:bg-gray-50 transition"
                        data-customer-name="{{ strtolower($customer->Customer_Name) }}"
                        data-contact-number="{{ $customer->Contact_Number ?? '' }}"
                        data-address="{{ strtolower($customer->address ?? '') }}">
                        
                        <td class="py-2 px-4 text-sm text-gray-800 font-medium">{{ $customer->Customer_Name }}</td>
                        <td class="py-2 px-4 text-sm text-gray-700">{{ $customer->Contact_Number ?? 'N/A' }}</td>
                        <td class="py-2 px-4 text-sm text-gray-700">{{ $customer->address ?? 'N/A' }}</td>
                        <td class="py-2 px-4 text-sm text-gray-500">{{ $customer->deleted_at->format('M d, Y') }}</td>

                        <!-- Action Buttons -->
                        <td class="py-2 px-4">
                            <!-- Restore Button (Icon Only) -->
                            <form action="{{ route('customers.restore', $customer->Customer_ID) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="text-green-600 hover:text-green-700 transition transform hover:scale-110"
                                    title="Restore"
                                    onclick="return confirm('Are you sure you want to restore this customer?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    
                    <!-- No Results Row (hidden by default) -->
                    <tr id="noResults" style="display: none;">
                        <td colspan="5" class="py-8 px-4 text-center text-gray-500">
                            No archived customers found
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>

</div>

<!-- Real-time Search Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        const tableBody = document.getElementById('customersTableBody');
        const rows = tableBody.querySelectorAll('tr:not(#noResults)');
        const noResults = document.getElementById('noResults');
        
        // Real-time search as you type
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;
            
            rows.forEach(row => {
                const customerName = row.getAttribute('data-customer-name') || '';
                const contactNumber = row.getAttribute('data-contact-number') || '';
                const address = row.getAttribute('data-address') || '';
                
                // Check if search term matches any field
                if (customerName.includes(searchTerm) || 
                    contactNumber.includes(searchTerm) || 
                    address.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            if (noResults) {
                if (visibleCount === 0 && searchTerm !== '') {
                    noResults.style.display = '';
                    noResults.querySelector('td').textContent = 'No archived customers found matching "' + searchTerm + '"';
                } else {
                    noResults.style.display = 'none';
                }
            }
        });
    }
});
</script>
@endsection