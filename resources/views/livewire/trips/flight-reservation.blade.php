<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Flight Reservations</h2>
            <p class="text-sm text-gray-500 mt-1">Manage all flight segments for this trip</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right">
                <p class="text-xs text-gray-500">{{ $segments->count() }} segment(s)</p>
                <p class="text-sm font-semibold text-gray-700">Total: {{ number_format($segments->sum('selling_price'), 2) }} / {{ number_format($segments->sum('cost_price'), 2) }}</p>
            </div>
            <button wire:click="create" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Flight
            </button>
        </div>
    </div>

    @if($segments->isEmpty())
        <div class="text-center py-16 bg-white rounded-2xl border-2 border-dashed border-gray-200">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            <h3 class="text-lg font-semibold text-gray-500 mb-1">No flights added yet</h3>
            <p class="text-sm text-gray-400">Click "Add Flight" to create the first flight segment</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($segments as $segment)
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    {{-- Header Bar --}}
                    <div class="flex items-center justify-between px-6 py-3 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                        <div class="flex items-center gap-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider
                                @if($segment->type === 'departure') bg-blue-50 text-blue-700
                                @elseif($segment->type === 'return') bg-emerald-50 text-emerald-700
                                @elseif($segment->type === 'domestic') bg-amber-50 text-amber-700
                                @else bg-purple-50 text-purple-700 @endif">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                {{ ucfirst($segment->type) }}
                            </span>
                            <div class="h-5 w-px bg-gray-200"></div>
                            <span class="text-sm font-bold text-gray-900">{{ $segment->airline }} {{ $segment->flight_number }}</span>
                            @if($segment->booking_reference)
                                <span class="text-xs text-gray-400">PNR: <span class="font-mono font-semibold text-gray-600">{{ $segment->booking_reference }}</span></span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($segment->status === 'confirmed') bg-green-50 text-green-700
                                @elseif($segment->status === 'pending') bg-yellow-50 text-yellow-700
                                @elseif($segment->status === 'cancelled') bg-red-50 text-red-700
                                @else bg-gray-50 text-gray-700 @endif">
                                {{ ucfirst($segment->status) }}
                            </span>
                            <div class="flex items-center gap-1 ml-2">
                                <button wire:click="edit('{{ $segment->id }}')" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="duplicate('{{ $segment->id }}')" class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Duplicate">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                                <button wire:click="delete('{{ $segment->id }}')" wire:confirm="Delete this flight segment?" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Body with 3 columns --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
                        {{-- Column 1: Route & Times --}}
                        <div class="p-5 space-y-4">
                            <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                Route
                            </h4>
                            <div class="flex items-center gap-3">
                                <div class="flex-1 text-center">
                                    <p class="text-lg font-bold text-gray-900">{{ $segment->departure_airport }}</p>
                                    <p class="text-xs text-gray-500">Departure</p>
                                    @if($segment->departure_terminal)
                                        <p class="text-xs font-medium text-gray-400">T{{ $segment->departure_terminal }}</p>
                                    @endif
                                </div>
                                <div class="flex-shrink-0 flex flex-col items-center">
                                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </div>
                                <div class="flex-1 text-center">
                                    <p class="text-lg font-bold text-gray-900">{{ $segment->arrival_airport }}</p>
                                    <p class="text-xs text-gray-500">Arrival</p>
                                    @if($segment->arrival_terminal)
                                        <p class="text-xs font-medium text-gray-400">T{{ $segment->arrival_terminal }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-100">
                                <div>
                                    <p class="text-xs text-gray-400">Departure</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $segment->departure_datetime?->format('D, d M Y · H:i') ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Arrival</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $segment->arrival_datetime?->format('D, d M Y · H:i') ?? '—' }}</p>
                                </div>
                            </div>
                        </div>

                        {{ -- Column 2: Booking Details -- }}
                        <div class="p-5 space-y-4">
                            <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Booking Info
                            </h4>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                                <div>
                                    <p class="text-xs text-gray-400">PNR / Booking Ref</p>
                                    <p class="text-sm font-mono font-semibold text-gray-800">{{ $segment->booking_reference ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Ticket Number</p>
                                    <p class="text-sm font-mono font-semibold text-gray-800">{{ $segment->ticket_number ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Class</p>
                                    <p class="text-sm font-semibold text-gray-800 capitalize">{{ $segment->class ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Cabin</p>
                                    <p class="text-sm font-semibold text-gray-800 capitalize">{{ $segment->cabin ?? '—' }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs text-gray-400">Fare Basis</p>
                                    <p class="text-sm font-mono font-semibold text-gray-800">{{ $segment->fare_basis ?? '—' }}</p>
                                </div>
                            </div>
                            @if($segment->supplier)
                                <div class="pt-2 border-t border-gray-100">
                                    <p class="text-xs text-gray-400">Supplier</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $segment->supplier->name }}</p>
                                </div>
                            @endif
                        </div>

                        {{ -- Column 3: Service & Financial -- }}
                        <div class="p-5 space-y-4">
                            <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                Service Details
                            </h4>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                                <div>
                                    <p class="text-xs text-gray-400">Baggage</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $segment->baggage ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Seat</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $segment->seat ?? '—' }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-xs text-gray-400">Meal</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $segment->meal ?? '—' }}</p>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-gray-100 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-400">Cost</span>
                                    <span class="text-sm font-semibold text-gray-800">{{ number_format($segment->cost_price, 2) }} {{ $segment->currency }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-400">Selling</span>
                                    <span class="text-sm font-semibold text-emerald-600">{{ number_format($segment->selling_price, 2) }} {{ $segment->currency }}</span>
                                </div>
                                @php $margin = $segment->selling_price - $segment->cost_price; @endphp
                                <div class="flex items-center justify-between pt-1 border-t border-gray-100">
                                    <span class="text-xs font-semibold text-gray-500">Profit</span>
                                    <span class="text-sm font-bold {{ $margin >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $margin >= 0 ? '+' : '' }}{{ number_format($margin, 2) }} {{ $segment->currency }}
                                    </span>
                                </div>
                            </div>
                            @if($segment->notes)
                                <div class="pt-2 border-t border-gray-100">
                                    <p class="text-xs text-gray-400">Notes</p>
                                    <p class="text-sm text-gray-600">{{ $segment->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Add/Edit Modal --}}
    <div x-cloak x-data="{ open: false }" x-on:open-modal.window="if ($event.detail === 'flight-form') open = true" x-on:close-modal.window="if ($event.detail === 'flight-form') open = false" x-on:keydown.escape.window="open = false" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
        <div x-show="open" x-transition class="relative min-h-screen flex items-start justify-center p-4 pt-8">
            <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-100 w-full max-w-3xl" x-on:click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">{{ $editingSegment ? 'Edit Flight' : 'New Flight' }}</h3>
                    <button type="button" x-on:click="open = false" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-6">
                    {{-- Segment Type --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Segment Type</label>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach(['departure', 'return', 'domestic', 'international'] as $opt)
                                <button type="button" wire:click="setType('{{ $opt }}')" class="px-3 py-2.5 text-sm font-medium rounded-xl border-2 transition-all
                                    {{ $type === $opt ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300' }}">
                                    {{ ucfirst($opt) }}
                                </button>
                            @endforeach
                        </div>
                        @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Two columns --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Left column: Airline info --}}
                        <div class="space-y-4">
                            <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400">Flight Identification</h4>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Airline *</label>
                                <input type="text" wire:model="airline" list="airlines" placeholder="e.g. EgyptAir" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                                <datalist id="airlines">
                                    <option value="EgyptAir"><option value="Emirates"><option value="Qatar Airways"><option value="Etihad"><option value="Turkish Airlines"><option value="Saudi Arabian Airlines"><option value="Air Arabia"><option value="flydubai"><option value="Flynas"><option value="Air Cairo"><option value="Nile Air"><option value="AlMasria Universal"><option value="British Airways"><option value="Lufthansa"><option value="Air France"><option value="Ryanair"><option value="Wizz Air"><option value="Pegasus Airlines"><option value="Gulf Air"><option value="Oman Air">
                                </datalist>
                                @error('airline') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Flight No. *</label>
                                    <input type="text" wire:model="flight_number" placeholder="e.g. MS123" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono" />
                                    @error('flight_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                                    <select wire:model="supplier_id" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">— None —</option>
                                    @foreach($suppliers as $s)
                                             <option value="{{ $s->id }}">{{ $s->company_name }}</option>
                                         @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select wire:model="status" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="confirmed">Confirmed</option>
                                    <option value="pending">Pending</option>
                                    <option value="ticketed">Ticketed</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="waitlisted">Waitlisted</option>
                                </select>
                            </div>
                        </div>

                        {{-- Right column: Booking refs --}}
                        <div class="space-y-4">
                            <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400">Booking References</h4>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">PNR / Booking Reference</label>
                                <input type="text" wire:model="booking_reference" placeholder="e.g. ABC123" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono uppercase" />
                                @error('booking_reference') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ticket Number</label>
                                <input type="text" wire:model="ticket_number" placeholder="e.g. 077-1234567890" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono" />
                                @error('ticket_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                                    <select wire:model="class" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="economy">Economy</option>
                                        <option value="premium_economy">Premium Economy</option>
                                        <option value="business">Business</option>
                                        <option value="first">First</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cabin</label>
                                    <input type="text" wire:model="cabin" placeholder="e.g. M, Y, C, J" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fare Basis</label>
                                <input type="text" wire:model="fare_basis" placeholder="e.g. YLE24A" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono" />
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-200 pt-5">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">Route & Schedule</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-4">
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Departure Airport *</label>
                                        <input type="text" wire:model="departure_airport" placeholder="e.g. CAI" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono uppercase" maxlength="4" />
                                        @error('departure_airport') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Terminal</label>
                                        <input type="text" wire:model="departure_terminal" placeholder="e.g. 2" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Departure Date & Time *</label>
                                    <input type="datetime-local" wire:model="departure_datetime" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                                    @error('departure_datetime') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div class="grid grid-cols-3 gap-2">
                                    <div class="col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Arrival Airport *</label>
                                        <input type="text" wire:model="arrival_airport" placeholder="e.g. SSH" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono uppercase" maxlength="4" />
                                        @error('arrival_airport') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Terminal</label>
                                        <input type="text" wire:model="arrival_terminal" placeholder="e.g. 1" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Arrival Date & Time *</label>
                                    <input type="datetime-local" wire:model="arrival_datetime" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                                    @error('arrival_datetime') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Service Details --}}
                    <div class="border-t border-gray-200 pt-5">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">Service Details</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Baggage</label>
                                <input type="text" wire:model="baggage" placeholder="e.g. 2x23kg" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Seat</label>
                                <input type="text" wire:model="seat" placeholder="e.g. 12A" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Meal</label>
                                <select wire:model="meal" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">— None —</option>
                                    <option value="Standard Meal">Standard Meal</option>
                                    <option value="Vegetarian">Vegetarian</option>
                                    <option value="Vegan">Vegan</option>
                                    <option value="Halal">Halal</option>
                                    <option value="Kosher">Kosher</option>
                                    <option value="Diabetic">Diabetic</option>
                                    <option value="Gluten-free">Gluten-free</option>
                                    <option value="Lactose-free">Lactose-free</option>
                                    <option value="Low-calorie">Low-calorie</option>
                                    <option value="Baby Meal">Baby Meal</option>
                                    <option value="Child Meal">Child Meal</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Financial --}}
                    <div class="border-t border-gray-200 pt-5">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">Financial</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-sm">$</span>
                                    <input type="number" step="0.01" min="0" wire:model="cost_price" class="w-full border border-gray-300 rounded-xl pl-7 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-sm">$</span>
                                    <input type="number" step="0.01" min="0" wire:model="selling_price" class="w-full border border-gray-300 rounded-xl pl-7 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                                <select wire:model="currency" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="EGP">EGP</option>
                                    <option value="GBP">GBP</option>
                                    <option value="SAR">SAR</option>
                                    <option value="AED">AED</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea wire:model="notes" rows="2" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Any additional notes..."></textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-200">
                        <button type="button" x-on:click="open = false" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors disabled:opacity-50">
                            <span wire:loading.remove wire:target="save">{{ $editingSegment ? 'Update Flight' : 'Add Flight' }}</span>
                            <span wire:loading wire:target="save" class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
