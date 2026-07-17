<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex-1 max-w-md">
            <div class="relative">
                <input type="text" placeholder="Search suppliers..." wire:model.live.debounce.300ms="search"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
        </div>
        <div class="flex gap-3">
            <select wire:model.live="filterType" class="border border-gray-300 rounded-lg text-sm px-3 py-2">
                <option value="">All Types</option>
                <option value="airline">Airline</option>
                <option value="hotel">Hotel</option>
                <option value="transfer_company">Transfer</option>
                <option value="visa_office">Visa Office</option>
                <option value="insurance_company">Insurance</option>
                <option value="tour_operator">Tour Operator</option>
                <option value="other">Other</option>
            </select>
            <select wire:model.live="filterStatus" class="border border-gray-300 rounded-lg text-sm px-3 py-2">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            <button wire:click="exportCsv" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Export
            </button>
            <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Add Supplier
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-medium cursor-pointer" wire:click="sortBy('supplier_code')">Code @if ($sortField === 'supplier_code')<span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</th>
                        <th class="px-4 py-3 font-medium cursor-pointer" wire:click="sortBy('company_name')">Company @if ($sortField === 'company_name')<span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</th>
                        <th class="px-4 py-3 font-medium">Type</th>
                        <th class="px-4 py-3 font-medium">Contact</th>
                        <th class="px-4 py-3 font-medium">Balance</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $supplier->supplier_code }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="text-blue-600 hover:text-blue-800 font-medium">{{ $supplier->company_name }}</a>
                                @if ($supplier->contact_person)<div class="text-xs text-gray-400">{{ $supplier->contact_person }}</div>@endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $supplier->type === 'hotel' ? 'bg-purple-100 text-purple-700' : ($supplier->type === 'airline' ? 'bg-sky-100 text-sky-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ str_replace('_', ' ', ucfirst($supplier->type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-gray-900">{{ $supplier->email ?: '—' }}</div>
                                <div class="text-xs text-gray-400">{{ $supplier->phone ?: '—' }}</div>
                            </td>
                            <td class="px-4 py-3 font-mono text-sm">{{ number_format($supplier->current_balance, 2) }}</td>
                            <td class="px-4 py-3">
                                <x-status-badge :status="$supplier->is_active ? 'active' : 'inactive'">{{ $supplier->is_active ? 'Active' : 'Inactive' }}</x-status-badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="p-1.5 text-gray-400 hover:text-amber-600 rounded-lg hover:bg-amber-50" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                    <button wire:click="delete('{{ $supplier->id }}')" wire:confirm="Are you sure you want to delete this supplier?" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-0"><x-empty-state icon="users" title="No suppliers found" message="Add your first supplier to get started." action='<a href="{{ route("suppliers.create") }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">Add Supplier</a>' /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($suppliers->hasPages())<div class="px-4 py-3 border-t border-gray-200">{{ $suppliers->links() }}</div>@endif
    </div>
</div>
