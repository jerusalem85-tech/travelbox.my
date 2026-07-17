@props(['status' => '', 'type' => ''])

@php
$classes = match($type ?: $status) {
    'enquiry', 'pending' => 'bg-yellow-100 text-yellow-700',
    'confirmed', 'active' => 'bg-blue-100 text-blue-700',
    'in_progress' => 'bg-indigo-100 text-indigo-700',
    'completed', 'paid', 'sent' => 'bg-green-100 text-green-700',
    'cancelled' => 'bg-red-100 text-red-700',
    'draft' => 'bg-gray-100 text-gray-700',
    'overdue' => 'bg-red-100 text-red-700',
    'inactive' => 'bg-gray-100 text-gray-500',
    default => 'bg-gray-100 text-gray-700',
};

$icons = match($type ?: $status) {
    'enquiry' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
    'pending' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
    'confirmed' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
    'paid', 'completed', 'sent' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
    'cancelled', 'overdue' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
    default => '',
};
@endphp

<span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-medium rounded-full {{ $classes }}" {{ $attributes }}>
    @if ($icons[$type ?: $status] ?? false)
        {!! $icons[$type ?: $status] !!}
    @endif
    {{ $slot }}
</span>
