@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 class="text-3xl font-extrabold text-green-700">Suppliers</h1>
        <div class="flex gap-3">
            <a href="{{ route('suppliers.create') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-semibold shadow-md transition transform hover:scale-105">
                + Add Supplier
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow-md p-3">
        <form action="{{ route('suppliers.index') }}" method="GET" class="flex gap-2" id="searchForm">
            <div class="flex-1 max-w-md">
                <input type="text" 
                       name="search" 
                       id="searchInput"
                       value="{{ request('search') }}"
                       placeholder="Search suppliers..." 
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>
            <button type="submit" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 text-sm rounded-lg font-semibold shadow-md transition transform hover:scale-105">
                Search
            </button>
            @if(request('search'))
            <a href="{{ route('suppliers.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 text-sm rounded-lg font-semibold shadow-md transition transform hover:scale-105">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-md animate-fade">
        {{ session('success') }}
    </div>
    @endif

    <!-- Suppliers Table -->
    <div class="overflow-x-auto bg-white rounded-2xl shadow-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-green-700 text-white rounded-t-xl">
                <tr>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Supplier Name</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Contact Person</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Contact Number</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Address</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Payment Terms</th>
                    <th class="py-3 px-4 text-left text-sm font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white" id="suppliersTableBody">
                @forelse($suppliers as $supplier)
                <tr class="hover:bg-green-50 transition" data-supplier-name="{{ strtolower($supplier->Supplier_Name) }}" data-contact-person="{{ strtolower($supplier->contact_person) }}" data-contact-number="{{ $supplier->contact_number }}">
                    <td class="py-2 px-4 text-sm text-gray-800 font-medium">{{ $supplier->Supplier_Name }}</td>
                    <td class="py-2 px-4 text-sm text-gray-700">{{ $supplier->contact_person }}</td>
                    <td class="py-2 px-4 text-sm text-gray-700">{{ $supplier->contact_number }}</td>
                    <td class="py-2 px-4 text-sm text-gray-700">{{ $supplier->address }}</td>
                    <td class="py-2 px-4 text-sm text-gray-700 font-semibold">{{ $supplier->payment_terms }}</td>

                    <!-- Action Buttons -->
                    <td class="py-2 px-4 flex gap-3">

                        <!-- Edit Button -->
                        <a href="{{ route('suppliers.edit', $supplier->Supplier_ID) }}"
                            class="text-yellow-600 hover:text-yellow-700 transition transform hover:scale-110"
                            title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a2.25 2.25 0 113.182 3.183L7.5 19.215 3 21l1.784-4.5 12.078-13.013z" />
                            </svg>
                        </a>

                        <!-- Archive Button (Soft Delete) -->
                        <form action="{{ route('suppliers.destroy', $supplier->Supplier_ID) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to archive this supplier? You can restore it later from the archive.')">
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
                @empty
                <tr id="noResults">
                    <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                        No suppliers available
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination (if you're using pagination) -->
    @if(method_exists($suppliers, 'links'))
    <div class="mt-4">
        {{ $suppliers->links() }}
    </div>
    @endif

</div>

<!-- Real-time Search Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('suppliersTableBody');
    const rows = tableBody.querySelectorAll('tr:not(#noResults)');
    const noResults = document.getElementById('noResults');
    
    // Real-time search as you type
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleCount = 0;
        
        rows.forEach(row => {
            const supplierName = row.getAttribute('data-supplier-name') || '';
            const contactPerson = row.getAttribute('data-contact-person') || '';
            const contactNumber = row.getAttribute('data-contact-number') || '';
            
            // Check if search term matches any field
            if (supplierName.includes(searchTerm) || 
                contactPerson.includes(searchTerm) || 
                contactNumber.includes(searchTerm)) {
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
                noResults.querySelector('td').textContent = 'No suppliers found matching "' + searchTerm + '"';
            } else if (visibleCount === 0 && searchTerm === '' && rows.length === 0) {
                noResults.style.display = '';
                noResults.querySelector('td').textContent = 'No suppliers available';
            } else {
                noResults.style.display = 'none';
            }
        }
    });
});
</script>
@endsection