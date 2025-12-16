@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-green-700">Inventory Audit Log</h1>
        <a href="{{ route('inventory.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Back to Inventory
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded shadow">
            <div class="flex items-center">
                <span class="material-icons text-blue-600 mr-3" style="font-size: 36px;">add_circle</span>
                <div>
                    <div class="text-sm text-blue-600 font-semibold">Stock In</div>
                    <div class="text-2xl font-bold text-blue-800">{{ $auditLogs->where('action', 'stock_in')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="bg-orange-50 p-4 rounded shadow">
            <div class="flex items-center">
                <span class="material-icons text-orange-600 mr-3" style="font-size: 36px;">remove_circle</span>
                <div>
                    <div class="text-sm text-orange-600 font-semibold">Stock Out</div>
                    <div class="text-2xl font-bold text-orange-800">{{ $auditLogs->where('action', 'stock_out')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="bg-green-50 p-4 rounded shadow">
            <div class="flex items-center">
                <span class="material-icons text-green-600 mr-3" style="font-size: 36px;">edit</span>
                <div>
                    <div class="text-sm text-green-600 font-semibold">Updates</div>
                    <div class="text-2xl font-bold text-green-800">{{ $auditLogs->where('action', 'update')->count() }}</div>
                </div>
            </div>
        </div>
        <div class="bg-purple-50 p-4 rounded shadow">
            <div class="flex items-center">
                <span class="material-icons text-purple-600 mr-3" style="font-size: 36px;">history</span>
                <div>
                    <div class="text-sm text-purple-600 font-semibold">Total Records</div>
                    <div class="text-2xl font-bold text-purple-800">{{ $auditLogs->total() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Log Table -->
    <div class="bg-white rounded shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-green-700 text-white">
                    <tr>
                        <th class="px-4 py-3 text-left">Date & Time</th>
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Action</th>
                        <th class="px-4 py-3 text-left">Field Changed</th>
                        <th class="px-4 py-3 text-left">Old Value</th>
                        <th class="px-4 py-3 text-left">New Value</th>
                        <th class="px-4 py-3 text-left">Remarks</th>
                        <th class="px-4 py-3 text-left">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($auditLogs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="text-sm">
                                    <div class="font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y') }}
                                    </div>
                                    <div class="text-gray-600">
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('h:i A') }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($log->product)
                                    <div class="font-semibold text-gray-900">{{ $log->product->Product_Name }}</div>
                                    <div class="text-xs text-gray-600">{{ $log->product->Category }}</div>
                                @else
                                    <span class="text-gray-400">Product Deleted</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center mr-2">
                                        {{ substr($log->cashier->fname ?? 'U', 0, 1) }}{{ substr($log->cashier->lname ?? 'N', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $log->cashier->fname ?? 'Unknown' }} {{ $log->cashier->lname ?? '' }}</div>
                                        <div class="text-xs text-gray-600">{{ ucfirst($log->cashier->role ?? 'N/A') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($log->action == 'stock_in')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded">Stock In</span>
                                @elseif($log->action == 'stock_out')
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded">Stock Out</span>
                                @elseif($log->action == 'adjustment')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded">Adjustment</span>
                                @elseif($log->action == 'update')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded">Update</span>
                                @elseif($log->action == 'create')
                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded">Create</span>
                                @elseif($log->action == 'delete')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded">Delete</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded">{{ ucfirst($log->action) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ ucwords(str_replace('_', ' ', $log->field_changed ?? 'N/A')) }}
                            </td>
                            <td class="px-4 py-3">
                                @if($log->old_value)
                                    <span class="text-red-600 font-semibold">{{ $log->old_value }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($log->new_value)
                                    <span class="text-green-600 font-semibold">{{ $log->new_value }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-sm">
                                {{ $log->remarks ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($log->product)
                                    <a href="{{ route('inventory.edit-history', $log->product_id) }}" class="text-blue-600 hover:underline text-sm">
                                        View History
                                    </a>
                                @else
                                    <span class="text-gray-400 text-sm">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <span class="material-icons text-gray-400 mb-4" style="font-size: 64px;">history</span>
                                    <p class="text-lg font-semibold">No audit logs found</p>
                                    <p class="text-sm">Inventory changes will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($auditLogs->hasPages())
            <div class="px-4 py-3 border-t">
                {{ $auditLogs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection