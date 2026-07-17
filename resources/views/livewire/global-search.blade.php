<div class="relative" x-data>
    <input type="text" wire:model.live="query" placeholder="Search trips, customers, suppliers..." class="w-72 bg-gray-100 border border-transparent focus:bg-white focus:border-gray-300 rounded-lg px-3 py-1.5 text-sm placeholder-gray-400" @click.away="$wire.hide()" />
    @if ($show && strlen($query) >= 2)
    <div class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl z-50 max-h-96 overflow-y-auto">
        @php $hasResults = array_sum(array_map('count', $results)) > 0; @endphp
        @if (!$hasResults)
        <p class="text-sm text-gray-500 text-center py-6">No results found.</p>
        @else
        @foreach (['trips' => 'Trips', 'customers' => 'Customers', 'suppliers' => 'Suppliers'] as $key => $label)
        @if (count($results[$key]) > 0)
        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50 border-b border-gray-100">{{ $label }}</div>
        @foreach ($results[$key] as $item)
        <a href="{{ route($key === 'trips' ? 'trips.show' : ($key === 'customers' ? 'customers.show' : 'suppliers.show'), $item['id']) }}" class="flex items-center gap-3 px-4 py-2.5 hover:bg-blue-50 border-b border-gray-50 last:border-0">
            @if ($key === 'trips')
            <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold">T</div>
            <div>
                <p class="text-sm font-medium text-gray-900">{{ $item['name'] ?: 'Untitled' }}</p>
                <p class="text-xs text-gray-500">{{ $item['trip_number'] }} &middot; {{ $item['destination'] ?: '—' }}</p>
            </div>
            @elseif ($key === 'customers')
            <div class="w-7 h-7 rounded-full bg-green-100 flex items-center justify-center text-green-600 text-xs font-bold">C</div>
            <div>
                <p class="text-sm font-medium text-gray-900">{{ $item['first_name'] }} {{ $item['last_name'] }}</p>
                <p class="text-xs text-gray-500">{{ $item['email'] ?: '—' }}</p>
            </div>
            @else
            <div class="w-7 h-7 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xs font-bold">S</div>
            <div>
                <p class="text-sm font-medium text-gray-900">{{ $item['company_name'] }}</p>
                <p class="text-xs text-gray-500">{{ $item['code'] }}</p>
            </div>
            @endif
        </a>
        @endforeach
        @endif
        @endforeach
        @endif
    </div>
    @endif
</div>
