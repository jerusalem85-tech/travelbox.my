<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex-1 max-w-md">
            <div class="relative">
                <input type="text" placeholder="Search trips..." wire:model.live.debounce.300ms="search"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500" />
                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
        </div>
        <div class="flex gap-2 items-center flex-wrap">
            <input type="date" wire:model.live="filterDateFrom" class="border border-gray-300 rounded-lg text-sm px-3 py-2 w-40" title="Start date from">
            <input type="date" wire:model.live="filterDateTo" class="border border-gray-300 rounded-lg text-sm px-3 py-2 w-40" title="Start date to">
            <select wire:model.live="filterStatus" class="border border-gray-300 rounded-lg text-sm px-3 py-2">
                <option value="">All Status</option>
                <option value="enquiry">Enquiry</option>
                <option value="confirmed">Confirmed</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <select wire:model.live="filterType" class="border border-gray-300 rounded-lg text-sm px-3 py-2">
                <option value="">All Types</option>
                <option value="package">Package</option>
                <option value="custom">Custom</option>
            </select>
            <button wire:click="exportCsv" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Export
            </button>
            <a href="{{ route('trips.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                New Trip
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div wire:loading wire:target="search, filterStatus, filterType, sortBy" class="px-4 py-3 border-b border-gray-200 bg-blue-50 text-blue-700 text-xs flex items-center gap-2">
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            Updating...
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 font-medium cursor-pointer" wire:click="sortBy('trip_number')">Trip # @if ($sortField === 'trip_number')<span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</th>
                        <th class="px-4 py-3 font-medium cursor-pointer" wire:click="sortBy('name')">Name @if ($sortField === 'name')<span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</th>
                        <th class="px-4 py-3 font-medium">Customer</th>
                        <th class="px-4 py-3 font-medium">Destination</th>
                        <th class="px-4 py-3 font-medium cursor-pointer" wire:click="sortBy('start_date')">Dates @if ($sortField === 'start_date')<span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>@endif</th>
                        <th class="px-4 py-3 font-medium text-right">Selling Price</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($trips as $trip)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $trip->trip_number }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('trips.show', $trip) }}" class="text-blue-600 hover:text-blue-800 font-medium">{{ $trip->name ?: '—' }}</a>
                            </td>
                            <td class="px-4 py-3">
                                @if ($trip->customer)
                                    <a href="{{ route('customers.show', $trip->customer) }}" class="text-gray-900 hover:text-blue-600">{{ $trip->customer->full_name }}</a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $trip->destination ?: '—' }}</td>
                            <td class="px-4 py-3 text-gray-600 text-xs">
                                @if ($trip->start_date){{ $trip->start_date->format('M d') }}@endif
                                @if ($trip->end_date) - {{ $trip->end_date->format('M d, Y') }}@endif
                            </td>
                            <td class="px-4 py-3 font-mono text-sm text-right">{{ number_format($trip->total_selling_price, 2) }}</td>
                            <td class="px-4 py-3">
                                <x-status-badge :status="$trip->status">{{ str_replace('_', ' ', ucfirst($trip->status)) }}</x-status-badge>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('trips.show', $trip) }}" class="p-1.5 text-gray-400 hover:text-blue-600" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                    <a href="{{ route('trips.edit', $trip) }}" class="p-1.5 text-gray-400 hover:text-amber-600" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-4 py-0"><x-empty-state icon="trip" title="No trips found" message="Get started by creating your first trip." action='<a href="{{ route("trips.create") }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">Create Trip</a>' /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($trips->hasPages())<div class="px-4 py-3 border-t border-gray-200">{{ $trips->links() }}</div>@endif
    </div>
</div>
