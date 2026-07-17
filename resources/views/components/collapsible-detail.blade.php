@props(['detail' => '', 'items' => []])

<div x-data="{ open: false }" class="text-sm">
    <button @click="open = !open" type="button" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 font-medium">
        {{ $detail }}
        <svg class="w-3.5 h-3.5 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" x-cloak class="mt-2 space-y-1 pl-2 border-l-2 border-gray-200">
        @foreach ($items as $item)
        <div class="flex justify-between py-1">
            <span class="text-gray-600">{{ $item['label'] }}</span>
            <span class="font-mono text-gray-900">{{ $item['value'] }}</span>
        </div>
        @endforeach
    </div>
</div>
