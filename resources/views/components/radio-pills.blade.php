@props(['label' => '', 'model' => '', 'options' => [], 'color' => 'blue'])

@php
$colors = [
    'blue' => 'bg-blue-100 text-blue-700 border-blue-200 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600',
    'green' => 'bg-green-100 text-green-700 border-green-200 peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-600',
    'amber' => 'bg-amber-100 text-amber-700 border-amber-200 peer-checked:bg-amber-600 peer-checked:text-white peer-checked:border-amber-600',
    'purple' => 'bg-purple-100 text-purple-700 border-purple-200 peer-checked:bg-purple-600 peer-checked:text-white peer-checked:border-purple-600',
];
$c = $colors[$color] ?? $colors['blue'];
@endphp

<div>
    @if ($label)<p class="text-sm font-medium text-gray-700 mb-2">{{ $label }}</p>@endif
    <div class="flex flex-wrap gap-2">
        @foreach ($options as $value => $text)
        <label class="cursor-pointer">
            <input type="radio" wire:model="{{ $model }}" value="{{ $value }}" class="sr-only peer">
            <span class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-full border {{ $c }} transition-all">{{ $text }}</span>
        </label>
        @endforeach
    </div>
    @error($model)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
