@props(['type' => 'info', 'text' => ''])

@php
$colors = [
    'info' => 'text-blue-600 bg-blue-50',
    'success' => 'text-green-600 bg-green-50',
    'warning' => 'text-amber-600 bg-amber-50',
    'danger' => 'text-red-600 bg-red-50',
];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] font-bold rounded ' . ($colors[$type] ?? $colors['info'])]) }}>
    {{ $text ?: $slot }}
</span>
