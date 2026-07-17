@props([
    'title' => 'Confirm action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'type' => 'danger',
    'event' => '',
    'action' => '',
])

@php
$btnColor = $type === 'danger' ? 'bg-red-600 hover:bg-red-700' : ($type === 'warning' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-blue-600 hover:bg-blue-700');
@endphp

<div x-data="{ open: false }"
     x-on:{{ $event }}.window="open = true"
     x-cloak
>
    <template x-teleport="body">
        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div class="fixed inset-0 bg-gray-500/75" @click="open = false"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6 z-10">
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                <p class="mt-2 text-sm text-gray-600">{{ $message }}</p>
                <div class="mt-6 flex justify-end gap-3">
                    <button @click="open = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">{{ $cancelText }}</button>
                    <button @click="open = false; $wire.{{ $action }}" class="px-4 py-2 text-sm font-medium text-white {{ $btnColor }} rounded-lg">{{ $confirmText }}</button>
                </div>
            </div>
        </div>
    </template>
</div>
