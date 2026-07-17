@props(['name' => '', 'title' => '', 'badge' => ''])

<button @click="tab = '{{ $name }}'" type="button" {{ $attributes->merge(['class' => 'px-4 py-2 text-sm font-medium rounded-lg transition-colors']) }}
        :class="tab === '{{ $name }}' ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'">
    {{ $title }}
    @if ($badge)<span class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full bg-gray-200 text-gray-600">{{ $badge }}</span>@endif
</button>
