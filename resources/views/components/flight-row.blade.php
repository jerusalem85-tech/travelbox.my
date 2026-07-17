@props(['trip', 'segment'])

@if ($trip && $segment)
<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
    <div class="flex items-center gap-4 min-w-0">
        <span class="text-xs font-medium uppercase {{ $segment->type === 'departure' ? 'text-blue-600' : 'text-green-600' }}">{{ $segment->type }}</span>
        <div class="min-w-0">
            <p class="text-sm font-medium text-gray-900">{{ $segment->airline }} {{ $segment->flight_number }}</p>
            <p class="text-xs text-gray-500 truncate">{{ $segment->departure_airport }} → {{ $segment->arrival_airport }} &middot; {{ $segment->departure_datetime?->format('M d, H:i') }}</p>
        </div>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <div class="text-right text-sm">
            <p class="font-mono">{{ number_format($segment->selling_price, 2) }}</p>
            @if ($segment->supplier)<p class="text-xs text-gray-400">{{ $segment->supplier->company_name }}</p>@endif
        </div>
        @if (isset($actions)){{ $actions }}@endif
    </div>
</div>
@endif
