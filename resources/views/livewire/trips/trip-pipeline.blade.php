<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900">Trip Pipeline</h2>
        <input type="text" wire:model.live="search" placeholder="Search trips..." class="w-64 border border-gray-300 rounded-lg px-3 py-1.5 text-sm" />
    </div>

    <div class="grid grid-cols-5 gap-4" style="min-height: 70vh;">
        @foreach ($statuses as $status)
        @php
            $labels = ['enquiry' => ['Enquiry', 'bg-yellow-100 text-yellow-700', 'border-yellow-300'], 'confirmed' => ['Confirmed', 'bg-blue-100 text-blue-700', 'border-blue-300'], 'in_progress' => ['In Progress', 'bg-indigo-100 text-indigo-700', 'border-indigo-300'], 'completed' => ['Completed', 'bg-green-100 text-green-700', 'border-green-300'], 'cancelled' => ['Cancelled', 'bg-red-100 text-red-700', 'border-red-300']];
            $label = $labels[$status];
        @endphp
        <div class="bg-gray-50 rounded-xl border border-gray-200 flex flex-col">
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-700 {{ explode(' ', $label[1])[0] }} px-2 py-0.5 rounded {{ $label[1] }}">{{ $label[0] }}</span>
                <span class="text-xs text-gray-400">{{ count($columns[$status]) }}</span>
            </div>
            <div class="p-3 space-y-3 flex-1 overflow-y-auto">
                @forelse ($columns[$status] as $trip)
                <div class="bg-white rounded-lg border {{ $label[2] }} p-3 shadow-sm">
                    <a href="{{ route('trips.show', $trip['id']) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600 block">{{ $trip['name'] ?: 'Untitled' }}</a>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $trip['trip_number'] }} &middot; {{ $trip['destination'] ?: '—' }}</p>
                    @if ($trip['start_date'])
                    <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($trip['start_date'])->format('M d') }} - {{ isset($trip['end_date']) ? \Carbon\Carbon::parse($trip['end_date'])->format('M d') : '—' }}</p>
                    @endif
                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100">
                        <span class="text-xs text-gray-500">{{ $trip['customer']['first_name'] ?? '—' }} {{ $trip['customer']['last_name'] ?? '' }}</span>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-xs text-blue-600 hover:underline">Move</button>
                            <div x-show="open" @click.outside="open = false" class="absolute right-0 top-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg z-10 py-1 min-w-[120px]">
                                @foreach (array_filter($statuses, fn($s) => $s !== $status) as $target)
                                <button wire:click="moveTo('{{ $trip['id'] }}', '{{ $target }}')" @click="open = false" class="block w-full text-left px-3 py-1.5 text-xs hover:bg-gray-50 {{ match($target) { 'cancelled' => 'text-red-600', 'completed' => 'text-green-600', default => 'text-gray-700' } }}">
                                    {{ str_replace('_', ' ', ucfirst($target)) }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-6">No trips</p>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>
