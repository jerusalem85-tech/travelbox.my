@props(['title' => '', 'description' => '', 'actions' => ''])

<div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
    <div>
        <h2 class="text-xl font-bold text-gray-900">{{ $title }}</h2>
        @if ($description)<p class="text-sm text-gray-500 mt-1">{{ $description }}</p>@endif
    </div>
    @if ($actions)<div class="flex items-center gap-2 shrink-0">{{ $actions }}</div>@endif
</div>
