<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200">
            <div class="flex items-center gap-3">
                <button wire:click="previousMonth"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <h2 class="text-xl font-semibold text-gray-900 min-w-[180px] text-center select-none">{{ $monthName }}</h2>
                <button wire:click="nextMonth"
                    class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
                <button wire:click="goToToday"
                    class="ml-2 px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100 transition-colors">
                    Today
                </button>
            </div>
            <div class="flex items-center gap-4 text-xs text-gray-400">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-yellow-400"></span> Enquiry</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Confirmed</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-400"></span> In Progress</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Completed</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-400"></span> Cancelled</span>
            </div>
        </div>

        <table class="w-full table-fixed border-collapse">
            <thead>
                <tr>
                    @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $i => $name)
                    <th class="py-3 text-xs font-semibold uppercase tracking-wider text-center bg-gray-50 border-b border-gray-200 {{ in_array($i, [0, 6]) ? 'text-red-400' : 'text-gray-500' }}">
                        {{ $name }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($weeks as $week)
                <tr>
                    @foreach ($week as $day)
                    <td class="border border-gray-200 align-top p-2 {{ $day['isCurrentMonth'] ? 'bg-white' : 'bg-gray-50/60' }} {{ $day['isToday'] ? 'bg-blue-50/70' : '' }}">
                        <div class="flex items-start justify-between mb-1.5">
                            <span class="inline-flex items-center justify-center w-9 h-9 text-lg font-bold leading-none
                                {{ $day['isToday'] ? 'bg-blue-600 text-white rounded-full' : ($day['isCurrentMonth'] ? 'text-gray-900' : 'text-gray-300') }}">
                                {{ $day['day'] }}
                            </span>
                            @if ($day['tripCount'] > 0)
                            <span class="text-[10px] font-semibold text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded-full leading-tight">{{ $day['tripCount'] }}</span>
                            @endif
                        </div>
                        <div class="space-y-1">
                            @php
                            $isExpanded = $expandedDate === $day['date'];
                            $visible = $isExpanded ? $day['trips'] : array_slice($day['trips'], 0, 2);
                            $hidden = $day['tripCount'] - count($visible);
                        @endphp
                            @foreach ($visible as $trip)
                            @php
                                $c = match($trip['status']) {
                                    'enquiry' => 'bg-yellow-50 text-yellow-700 border-l-yellow-400',
                                    'confirmed' => 'bg-blue-50 text-blue-700 border-l-blue-500',
                                    'in_progress' => 'bg-amber-50 text-amber-700 border-l-amber-400',
                                    'completed' => 'bg-emerald-50 text-emerald-700 border-l-emerald-500',
                                    'cancelled' => 'bg-red-50 text-red-400 border-l-red-400 line-through',
                                    default => 'bg-gray-50 text-gray-600 border-l-gray-400',
                                };
                            @endphp
                            <a href="{{ route('trips.show', $trip['id']) }}"
                               class="block pl-2 border-l-[3px] rounded-r text-[11px] font-medium py-1 pr-1 truncate hover:shadow-sm transition-shadow {{ $c }}"
                               title="{{ $trip['name'] }}">
                                {{ $trip['trip_number'] }}
                            </a>
                            @endforeach
                            @if ($hidden > 0)
                            <button wire:click="toggleExpand('{{ $day['date'] }}')"
                                    class="inline text-[11px] font-medium text-blue-600 hover:text-blue-800 hover:underline cursor-pointer">
                                +{{ $hidden }} more
                            </button>
                            @endif
                            @if ($isExpanded && $day['tripCount'] > 2)
                            <button wire:click="toggleExpand('{{ $day['date'] }}')"
                                    class="inline text-[11px] font-medium text-gray-500 hover:text-gray-700 hover:underline cursor-pointer ml-1">
                                Show less
                            </button>
                            @endif
                        </div>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
