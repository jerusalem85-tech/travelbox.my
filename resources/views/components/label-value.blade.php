@props(['label' => '', 'value' => '—', 'class' => ''])

<div class="flex items-center justify-between text-sm {{ $class }}">
    <span class="text-gray-500">{{ $label }}</span>
    <span class="font-medium text-gray-900 {{ $attributes->get('class') }}">{{ $value }}</span>
</div>
