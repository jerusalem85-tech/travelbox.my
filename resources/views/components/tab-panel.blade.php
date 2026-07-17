@props(['name' => ''])

<div x-show="tab === '{{ $name }}'" x-cloak {{ $attributes }}>
    {{ $slot }}
</div>
