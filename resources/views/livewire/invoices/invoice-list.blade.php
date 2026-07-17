<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Invoices</h2>
            <p class="text-sm text-gray-500 mt-1">Manage all invoices</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            New Invoice
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <div class="flex flex-wrap gap-3">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search invoices..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64" />
                <select wire:model.live="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="overdue">Overdue</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th wire:click="sortBy('invoice_number')" class="px-4 py-3 font-medium cursor-pointer hover:text-gray-700">Invoice #</th>
                        <th wire:click="sortBy('customer_id')" class="px-4 py-3 font-medium cursor-pointer hover:text-gray-700">Customer</th>
                        <th wire:click="sortBy('issue_date')" class="px-4 py-3 font-medium cursor-pointer hover:text-gray-700">Date</th>
                        <th wire:click="sortBy('due_date')" class="px-4 py-3 font-medium cursor-pointer hover:text-gray-700">Due Date</th>
                        <th wire:click="sortBy('total')" class="px-4 py-3 font-medium cursor-pointer hover:text-gray-700 text-right">Total</th>
                        <th wire:click="sortBy('status')" class="px-4 py-3 font-medium cursor-pointer hover:text-gray-700">Status</th>
                        <th class="px-4 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $invoice->invoice_number }}</td>
                        <td class="px-4 py-3">
                            <span class="text-gray-900">{{ $invoice->customer?->full_name ?: '—' }}</span>
                            @if ($invoice->trip)<div class="text-xs text-gray-400">{{ $invoice->trip->name }}</div>@endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $invoice->issue_date?->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $invoice->due_date?->format('M d, Y') }}</td>
                        <td class="px-4 py-3 font-mono text-right">{{ number_format($invoice->total, 2) }}</td>
                        <td class="px-4 py-3">
                        <x-status-badge :status="$invoice->status">{{ ucfirst($invoice->status) }}</x-status-badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <a href="{{ route('invoices.show', $invoice) }}" class="px-2 py-1 text-xs text-blue-600 hover:underline">View</a>
                                <a href="{{ route('invoices.edit', $invoice) }}" class="px-2 py-1 text-xs text-amber-600 hover:underline">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No invoices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($invoices->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
