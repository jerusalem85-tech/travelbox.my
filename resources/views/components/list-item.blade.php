@props(['label' => '', 'action' => ''])

<div class="flex items-center gap-2 px-4 py-3 bg-white border border-gray-200 rounded-lg {{ $attributes->get('class') }}">
    <div class="flex-1 min-w-0">
        @if ($label)<p class="text-sm font-medium text-gray-900 truncate">{{ $label }}</p>@endif
        {{ $slot }}
    </div>
    @if ($action)<div class="shrink-0">{{ $action }}</div>@endif
</div>
