@props(['trip', 'booking'])

@if ($trip && $booking)
<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
    <div class="min-w-0">
        <p class="text-sm font-medium text-gray-900">{{ $booking->hotel_name }}</p>
        <p class="text-xs text-gray-500 truncate">{{ $booking->city }} &middot; {{ $booking->check_in?->format('M d') }} - {{ $booking->check_out?->format('M d') }} &middot; {{ $booking->room_type }} x{{ $booking->number_of_rooms }}</p>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <div class="text-right text-sm">
            <p class="font-mono">{{ number_format($booking->selling_price, 2) }}</p>
            @if ($booking->supplier)<p class="text-xs text-gray-400">{{ $booking->supplier->company_name }}</p>@endif
        </div>
        @if (isset($actions)){{ $actions }}@endif
    </div>
</div>
@endif
