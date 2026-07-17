@props(['trip', 'booking'])

@if ($trip && $booking)
<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
    <div class="min-w-0">
        <p class="text-sm font-medium text-gray-900">{{ $booking->pickup_location }} → {{ $booking->dropoff_location }}</p>
        <p class="text-xs text-gray-500">{{ $booking->pickup_datetime?->format('M d, Y H:i') }} &middot; {{ $booking->vehicle_type }}</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <div class="text-right text-sm"><p class="font-mono">{{ number_format($booking->selling_price, 2) }}</p></div>
        @if (isset($actions)){{ $actions }}@endif
    </div>
</div>
@endif
