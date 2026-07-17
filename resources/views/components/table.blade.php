@props(['headers' => [], 'rows' => [], 'actions' => false, 'sortable' => false, 'sortField' => '', 'sortDirection' => 'asc', 'checkbox' => false])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden']) }}>
    @if (count($rows) > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b border-gray-200 bg-gray-50">
                    @if ($checkbox)<th class="px-4 py-3 w-10"><input type="checkbox" class="rounded border-gray-300"></th>@endif
                    @foreach ($headers as $key => $label)
                    <th class="px-4 py-3 font-medium {{ $sortable && $attributes->has('wire:click') ? 'cursor-pointer hover:text-gray-700' : '' }}"
                        @if ($sortable && $key !== 'actions')
                        wire:click="sortBy('{{ $key }}')"
                        @endif
                    >
                        {{ $label }}
                        @if ($sortable && $sortField === $key)
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    @endforeach
                    @if ($actions)<th class="px-4 py-3 font-medium text-right">Actions</th>@endif
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    @if ($checkbox)<td class="px-4 py-3"><input type="checkbox" class="rounded border-gray-300"></td>@endif
                    {{ $row }}
                    @if ($actions)<td class="px-4 py-3 text-right">{{ $actions }}</td>@endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    {{ $slot }}
    @endif
</div>
