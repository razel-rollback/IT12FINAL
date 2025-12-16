@extends('layouts.app')

@section('title', 'All Cashiers')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <span class="material-icons text-blue-600 text-4xl mr-3">people</span>
                <h2 class="text-2xl font-bold text-gray-800">All Cashiers</h2>
            </div>
            <a href="{{ route('users.create-cashier') }}" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center">
                <span class="material-icons mr-2">person_add</span>
                Create New Cashier
            </a>
        </div>

        <!-- Search Bar -->
        @if($cashiers->count() > 0)
        <div class="bg-gray-50 rounded-lg p-3 mb-4">
            <div class="flex gap-2">
                <div class="flex-1 max-w-md">
                    <input type="text" 
                           id="searchInput"
                           placeholder="Search cashiers..." 
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-blue-50 border-b">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">First Name</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Last Name</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Email</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Contact Number</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Created</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody id="cashiersTableBody">
                    @forelse($cashiers as $cashier)
                        <tr class="border-b hover:bg-gray-50"
                            data-fname="{{ strtolower($cashier->fname) }}"
                            data-lname="{{ strtolower($cashier->lname) }}"
                            data-email="{{ strtolower($cashier->email) }}"
                            data-contact="{{ $cashier->contact_number ?? '' }}">
                            
                            <td class="px-4 py-3 text-sm font-medium">{{ $cashier->fname }}</td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $cashier->lname }}</td>
                            <td class="px-4 py-3 text-sm">{{ $cashier->email }}</td>
                            <td class="px-4 py-3 text-sm">{{ $cashier->contact_number ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ $cashier->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('users.destroy', $cashier->User_ID) }}" method="POST" 
                                    onsubmit="return confirm('Are you sure you want to delete this cashier?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm flex items-center mx-auto">
                                        <span class="material-icons text-sm mr-1">delete</span>
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr id="noResults">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <span class="material-icons text-6xl text-gray-300 mb-2">person_off</span>
                                    <p class="text-lg">No cashiers found.</p>
                                    <a href="{{ route('users.create-cashier') }}" class="mt-4 text-blue-600 hover:text-blue-700 underline">
                                        Create your first cashier account
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <div class="text-sm text-gray-600">
                <span class="font-semibold text-blue-600">{{ $cashiers->count() }}</span> Total Cashiers
            </div>
            <a href="{{ route('users.index') }}" class="text-sm text-gray-600 hover:text-gray-800 underline">
                View All Users
            </a>
        </div>
    </div>
</div>

<!-- Real-time Search Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        const tableBody = document.getElementById('cashiersTableBody');
        const rows = tableBody.querySelectorAll('tr:not(#noResults)');
        const noResults = document.getElementById('noResults');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;
            
            rows.forEach(row => {
                const fname = row.getAttribute('data-fname') || '';
                const lname = row.getAttribute('data-lname') || '';
                const email = row.getAttribute('data-email') || '';
                const contact = row.getAttribute('data-contact') || '';
                
                if (fname.includes(searchTerm) || 
                    lname.includes(searchTerm) ||
                    email.includes(searchTerm) || 
                    contact.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            if (noResults && rows.length > 0) {
                if (visibleCount === 0 && searchTerm !== '') {
                    noResults.style.display = '';
                    noResults.querySelector('td').innerHTML = `
                        <div class="flex flex-col items-center">
                            <p class="text-lg">No cashiers found matching "${searchTerm}"</p>
                        </div>
                    `;
                } else {
                    noResults.style.display = 'none';
                }
            }
        });
    }
});
</script>
@endsection