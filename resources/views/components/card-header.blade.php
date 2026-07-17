@props(['title' => '', 'action' => ''])

<div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
    <h3 class="text-sm font-semibold text-gray-900">{{ $title }}</h3>
    @if ($action)<div class="flex items-center gap-2">{{ $action }}</div>@endif
</div>
{{ $slot }}
