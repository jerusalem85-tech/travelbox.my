@props(['title' => '', 'items' => []])

@php
$groupIcons = [
    'Trips' => 'trips', 'Contacts' => 'customers', 'Finance' => 'payments', 'Reports' => 'reports', 'System' => 'settings',
];
@endphp

<div class="pt-3">
    @if ($title)<p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">{{ $title }}</p>@endif
    <nav class="space-y-1">
        {{ $slot }}
    </nav>
</div>
