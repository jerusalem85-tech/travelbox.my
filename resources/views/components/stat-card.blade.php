@props(['title' => '', 'description' => '', 'icon' => '', 'color' => 'blue', 'value' => '—', 'trend' => ''])

@php
$colors = [
    'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
    'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600'],
    'amber' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600'],
    'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
    'emerald' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
    'red' => ['bg' => 'bg-red-100', 'text' => 'text-red-600'],
    'indigo' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600'],
    'gray' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-600'],
];
$c = $colors[$color] ?? $colors['blue'];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200 p-5']) }}>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">{{ $title }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $value }}</p>
            @if ($description)<p class="text-xs {{ $trend ? ($trend > 0 ? 'text-green-500' : 'text-red-500') : 'text-gray-400' }} mt-2">{{ $description }}</p>@endif
        </div>
        @if ($icon)
        <div class="{{ $c['bg'] }} p-3 rounded-lg">
            {!! $icon !!}
        </div>
        @endif
    </div>
</div>
