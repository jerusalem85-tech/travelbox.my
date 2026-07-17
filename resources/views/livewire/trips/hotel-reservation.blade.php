<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Hotel Bookings</h2>
            <p class="text-sm text-gray-500 mt-1">Manage all hotel reservations for this trip</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right">
                <p class="text-xs text-gray-500">{{ $bookings->count() }} booking(s)</p>
                <p class="text-sm font-semibold text-gray-700">Total: {{ number_format($bookings->sum('selling_price'), 2) }} / {{ number_format($bookings->sum('cost_price'), 2) }}</p>
            </div>
            <button wire:click="create" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-xl hover:bg-emerald-700 transition-colors shadow-sm shadow-emerald-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Hotel
            </button>
        </div>
    </div>

    @if($bookings->isEmpty())
        <div class="text-center py-16 bg-white rounded-2xl border-2 border-dashed border-gray-200">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
            <h3 class="text-lg font-semibold text-gray-500 mb-1">No hotels added yet</h3>
            <p class="text-sm text-gray-400">Click "Add Hotel" to create the first hotel booking</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($bookings as $booking)
                @php $nights = $booking->check_in && $booking->check_out ? $booking->check_in->diffInDays($booking->check_out) : 0; @endphp
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    {{-- Header Bar --}}
                    <div class="flex items-center justify-between px-6 py-3 bg-gradient-to-r from-emerald-50 to-white border-b border-emerald-100">
                        <div class="flex items-center gap-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold tracking-wider bg-emerald-50 text-emerald-700">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                                Hotel
                            </span>
                            <div class="h-5 w-px bg-emerald-200"></div>
                            <span class="text-sm font-bold text-gray-900">{{ $booking->hotel_name }}</span>
                            @if($booking->city)
                                <span class="text-xs text-gray-400">{{ $booking->city }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($booking->status === 'confirmed') bg-green-50 text-green-700
                                @elseif($booking->status === 'pending') bg-yellow-50 text-yellow-700
                                @elseif($booking->status === 'cancelled') bg-red-50 text-red-700
                                @else bg-gray-50 text-gray-700 @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                            <div class="flex items-center gap-1 ml-2">
                                <button wire:click="edit('{{ $booking->id }}')" class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="duplicate('{{ $booking->id }}')" class="p-1.5 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Duplicate">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </button>
                                <a href="{{ route('pdfs.hotel-voucher', [$trip, $booking]) }}" target="_blank" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Voucher PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </a>
                                <button wire:click="delete('{{ $booking->id }}')" wire:confirm="Delete this hotel booking?" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Body with 3 columns --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
                        {{-- Column 1: Hotel Info & Dates --}}
                        <div class="p-5 space-y-4">
                            <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                                Hotel & Stay
                            </h4>
                            <div>
                                <p class="text-lg font-bold text-gray-900">{{ $booking->hotel_name }}</p>
                                @if($booking->address)
                                    <p class="text-sm text-gray-500 mt-1">{{ $booking->address }}</p>
                                @endif
                                @if($booking->city)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $booking->city }}</p>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-gray-100">
                                <div>
                                    <p class="text-xs text-gray-400">Check-in</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $booking->check_in?->format('D, d M Y') ?? '—' }}</p>
                                    @if($booking->check_in_time)
                                        <p class="text-xs text-gray-400">at {{ $booking->check_in_time }}</p>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Check-out</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $booking->check_out?->format('D, d M Y') ?? '—' }}</p>
                                    @if($booking->check_out_time)
                                        <p class="text-xs text-gray-400">at {{ $booking->check_out_time }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-sm">
                                <span class="font-semibold text-emerald-600">{{ $nights }}</span>
                                <span class="text-gray-400">{{ $nights === 1 ? 'night' : 'nights' }}</span>
                                <span class="text-gray-300 mx-1">|</span>
                                <span class="text-gray-600">{{ $booking->number_of_rooms }} {{ $booking->number_of_rooms === 1 ? 'room' : 'rooms' }}</span>
                            </div>
                            @if($booking->latitude && $booking->longitude)
                                <a href="https://www.google.com/maps?q={{ $booking->latitude }},{{ $booking->longitude }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 hover:text-blue-700">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    View on Google Maps
                                </a>
                            @endif
                        </div>

                        {{-- Column 2: Booking Details --}}
                        <div class="p-5 space-y-4">
                            <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Booking Info
                            </h4>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-3">
                                <div>
                                    <p class="text-xs text-gray-400">Room Type</p>
                                    <p class="text-sm font-semibold text-gray-800 capitalize">{{ $booking->room_type ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Meal Plan</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ ['room_only' => 'Room Only', 'bb' => 'Bed & Breakfast', 'hb' => 'Half Board', 'fb' => 'Full Board', 'ai' => 'All Inclusive', 'uai' => 'Ultra All Inclusive'][$booking->meal_plan] ?? $booking->meal_plan ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Booking Ref</p>
                                    <p class="text-sm font-mono font-semibold text-gray-800">{{ $booking->booking_reference ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400">Confirmation #</p>
                                    <p class="text-sm font-mono font-semibold text-gray-800">{{ $booking->confirmation_number ?? '—' }}</p>
                                </div>
                            </div>
                            @if($booking->supplier)
                                <div class="pt-2 border-t border-gray-100">
                                    <p class="text-xs text-gray-400">Supplier</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $booking->supplier->name }}</p>
                                </div>
                            @endif
                            @if($booking->cancellation_policy)
                                <div class="pt-2 border-t border-gray-100">
                                    <p class="text-xs text-gray-400">Cancellation Policy</p>
                                    <p class="text-sm text-gray-600">{{ $booking->cancellation_policy }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Column 3: Financial --}}
                        <div class="p-5 space-y-4">
                            <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                Pricing
                            </h4>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-400">Cost</span>
                                    <span class="text-sm font-semibold text-gray-800">{{ number_format($booking->cost_price, 2) }} {{ $booking->currency }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-400">Selling</span>
                                    <span class="text-sm font-semibold text-emerald-600">{{ number_format($booking->selling_price, 2) }} {{ $booking->currency }}</span>
                                </div>
                                @php $margin = $booking->selling_price - $booking->cost_price; @endphp
                                <div class="flex items-center justify-between pt-1 border-t border-gray-100">
                                    <span class="text-xs font-semibold text-gray-500">Profit</span>
                                    <span class="text-sm font-bold {{ $margin >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ $margin >= 0 ? '+' : '' }}{{ number_format($margin, 2) }} {{ $booking->currency }}
                                    </span>
                                </div>
                                @if($nights > 0 && $booking->selling_price > 0)
                                    <div class="pt-1 border-t border-gray-100">
                                        <p class="text-xs text-gray-400">Price per night</p>
                                        <p class="text-sm font-semibold text-gray-700">{{ number_format($booking->selling_price / $nights, 2) }} {{ $booking->currency }}</p>
                                    </div>
                                @endif
                            </div>
                            @if($booking->notes)
                                <div class="pt-2 border-t border-gray-100">
                                    <p class="text-xs text-gray-400">Notes</p>
                                    <p class="text-sm text-gray-600">{{ $booking->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Add/Edit Modal --}}
    <div x-cloak x-data="{ open: false }" x-on:open-modal.window="if ($event.detail === 'hotel-form') open = true" x-on:close-modal.window="if ($event.detail === 'hotel-form') open = false" x-on:keydown.escape.window="open = false" x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>
        <div x-show="open" x-transition class="relative min-h-screen flex items-start justify-center p-4 pt-8">
            <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-100 w-full max-w-3xl" x-on:click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900">{{ $editingBooking ? 'Edit Hotel' : 'New Hotel' }}</h3>
                    <button type="button" x-on:click="open = false" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-6">
                    {{-- Hotel Name & City --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hotel Name *</label>
                            <input type="text" wire:model="hotel_name" placeholder="e.g. Hilton Sharm El Sheikh" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                            @error('hotel_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" wire:model="city" placeholder="e.g. Sharm El Sheikh" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                        </div>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" wire:model="address" placeholder="Hotel address / location" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                    </div>

                    {{-- Stay Dates --}}
                    <div class="border-t border-gray-200 pt-5">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">Stay Period</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Check-in *</label>
                                <input type="date" wire:model="check_in" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                                @error('check_in') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Check-in Time</label>
                                <input type="time" wire:model="check_in_time" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Check-out *</label>
                                <input type="date" wire:model="check_out" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                                @error('check_out') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Check-out Time</label>
                                <input type="time" wire:model="check_out_time" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                            </div>
                        </div>
                    </div>

                    {{-- Room & Booking Details --}}
                    <div class="border-t border-gray-200 pt-5">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">Room & Booking</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Room Type</label>
                                <select wire:model="room_type" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">— Select —</option>
                                    <option value="standard">Standard</option>
                                    <option value="superior">Superior</option>
                                    <option value="deluxe">Deluxe</option>
                                    <option value="junior_suite">Junior Suite</option>
                                    <option value="suite">Suite</option>
                                    <option value="executive">Executive</option>
                                    <option value="presidential">Presidential</option>
                                    <option value="family_room">Family Room</option>
                                    <option value="connecting">Connecting Rooms</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Meal Plan</label>
                                <select wire:model="meal_plan" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">— None —</option>
                                    <option value="room_only">Room Only</option>
                                    <option value="bb">Bed & Breakfast</option>
                                    <option value="hb">Half Board</option>
                                    <option value="fb">Full Board</option>
                                    <option value="ai">All Inclusive</option>
                                    <option value="uai">Ultra All Inclusive</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Rooms</label>
                                <input type="number" wire:model="number_of_rooms" min="1" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Booking Reference</label>
                                <input type="text" wire:model="booking_reference" placeholder="e.g. HTL12345" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Confirmation Number</label>
                                <input type="text" wire:model="confirmation_number" placeholder="Hotel confirmation #" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono" />
                            </div>
                        </div>
                    </div>

                    {{-- Location & Supplier --}}
                    <div class="border-t border-gray-200 pt-5">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">Location & Supplier</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                                <input type="text" wire:model="latitude" placeholder="e.g. 27.9654" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono" />
                                @error('latitude') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                                <input type="text" wire:model="longitude" placeholder="e.g. 34.5718" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 font-mono" />
                                @error('longitude') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                                <select wire:model="supplier_id" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="">— None —</option>
                                    @foreach($suppliers as $s)
                                         <option value="{{ $s->id }}">{{ $s->company_name }}</option>
                                     @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Status & Cancellation --}}
                    <div class="border-t border-gray-200 pt-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select wire:model="status" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                    <option value="confirmed">Confirmed</option>
                                    <option value="pending">Pending</option>
                                    <option value="guaranteed">Guaranteed</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="no_show">No Show</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cancellation Policy</label>
                                <textarea wire:model="cancellation_policy" rows="2" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Free cancellation up to 24 hours before check-in..."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Financial --}}
                    <div class="border-t border-gray-200 pt-5">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-4">Pricing</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-sm">$</span>
                                    <input type="number" step="0.01" min="0" wire:model="cost_price" class="w-full border border-gray-300 rounded-xl pl-7 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-sm">$</span>
                                    <input type="number" step="0.01" min="0" wire:model="selling_price" class="w-full border border-gray-300 rounded-xl pl-7 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                                <select wire:model="currency" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
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
                        <textarea wire:model="notes" rows="2" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Special requests, room preferences..."></textarea>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-200">
                        <button type="button" x-on:click="open = false" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition-colors disabled:opacity-50">
                            <span wire:loading.remove wire:target="save">{{ $editingBooking ? 'Update Hotel' : 'Add Hotel' }}</span>
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
