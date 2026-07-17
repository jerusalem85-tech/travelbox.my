@props(['id' => '', 'title' => '', 'active' => false])

<div x-data="{ tab: '{{ $active }}' }" {{ $attributes }}>
    {{ $slot }}
</div>
