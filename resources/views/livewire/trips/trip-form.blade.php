<div>
    <div class="max-w-7xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold">
                        {{ $editing ? 'Edit Trip' : 'New Trip' }}
                        @if ($editing)
                        <span class="ml-3 inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-sm rounded-lg text-sm font-mono tracking-wider">
                            {{ $trip->trip_number }}
                        </span>
                        @endif
                    </h2>
                    <p class="text-blue-100 text-sm mt-1">{{ $editing ? 'Update trip details and services below' : 'Create a new trip record' }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ $editing ? route('trips.show', $trip) : route('trips.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>

        <form wire:submit="save" class="space-y-4">
            {{-- Trip Information Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-t-4 border-t-blue-500">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900"><i class="fas fa-info-circle text-blue-500 mr-2"></i> Trip Information</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-0.5">Customer *</label>
                            <div class="flex gap-2">
                                <select wire:model="customer_id" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Customer...</option>
                                    @foreach ($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->full_name }}{{ $c->company_name ? ' ('.$c->company_name.')' : '' }}</option>
                                    @endforeach
                                </select>
                                <button type="button" wire:click="$set('showQuickCustomer', true)" class="px-3 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 shrink-0" title="Quick add customer"><i class="fas fa-plus"></i></button>
                            </div>
                            @error('customer_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-0.5">Trip Name</label>
                            <input type="text" wire:model="name" placeholder="e.g. Summer Vacation 2026" class="w-full border border-gray-300 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-0.5">Destination</label>
                            <div class="flex gap-1">
                                <input type="text" wire:model.blur="destination" placeholder="e.g. Istanbul" class="flex-1 border border-gray-300 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500" />
                                <button type="button" wire:click="fetchCoordinates" wire:loading.attr="disabled" class="px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 shrink-0" title="Auto-detect coordinates"><i wire:loading.remove wire:target="fetchCoordinates" class="fas fa-map-pin"></i><i wire:loading wire:target="fetchCoordinates" class="fas fa-spinner fa-spin"></i></button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-0.5">Coordinates</label>
                            <div class="flex gap-2">
                                <input type="number" step="any" wire:model="latitude" placeholder="Latitude" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-blue-500" />
                                <input type="number" step="any" wire:model="longitude" placeholder="Longitude" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-blue-500" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-0.5">Status</label>
                            <select wire:model="status" class="w-full border border-gray-300 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="enquiry">Enquiry</option><option value="confirmed">Confirmed</option>
                                <option value="in_progress">In Progress</option><option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-0.5">Type</label>
                            <select wire:model="type" class="w-full border border-gray-300 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="custom">Custom</option><option value="package">Package</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-0.5">Start Date</label>
                            <input type="date" wire:model="start_date" class="w-full border border-gray-300 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-0.5">End Date</label>
                            <input type="date" wire:model="end_date" class="w-full border border-gray-300 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-0.5">Currency</label>
                            <select wire:model="currency" class="w-full border border-gray-300 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500">
                                <option value="USD">USD</option><option value="ILS">ILS</option><option value="JOD">JOD</option><option value="EUR">EUR</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Financial Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-t-4 border-t-emerald-500">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900"><i class="fas fa-coins text-emerald-500 mr-2"></i> Financial</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-0.5">Total Selling Price</label>
                            <input type="number" step="0.01" min="0" wire:model="total_selling_price" class="w-full border border-gray-300 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-0.5">Total Cost Price</label>
                            <input type="number" step="0.01" min="0" wire:model="total_cost_price" class="w-full border border-gray-300 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500" />
                        </div>
                    </div>
                    @if (!$editing && (float)$total_selling_price > 0)
                    <div class="mt-3 p-3 bg-emerald-50 rounded-lg border border-emerald-200">
                        <p class="text-sm text-emerald-700 font-medium">Expected profit: <span class="font-bold">{{ number_format((float)$total_selling_price - (float)$total_cost_price, 2) }}</span> ({{ (float)$total_cost_price > 0 ? number_format(((float)$total_selling_price - (float)$total_cost_price) / (float)$total_selling_price * 100, 1) : '100' }}% margin)</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Notes Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-t-4 border-t-gray-400">
                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900"><i class="fas fa-sticky-note text-gray-500 mr-2"></i> Notes</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-0.5">Notes</label>
                            <textarea wire:model="notes" rows="3" class="w-full border border-gray-300 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-0.5">Internal Notes</label>
                            <textarea wire:model="internal_notes" rows="3" class="w-full border border-gray-300 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Map Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-t-4 border-t-purple-400">
                <div class="px-4 py-3 flex items-center justify-between border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Pin Location</span>
                    </div>
                    @if ($latitude && $longitude)
                    <span class="text-xs text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg font-medium"><i class="fas fa-check-circle mr-1"></i>{{ $latitude }}, {{ $longitude }}</span>
                    @else
                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-lg">Not set</span>
                    @endif
                </div>
                <div wire:ignore id="trip-form-map" class="w-full h-64" style="cursor: crosshair;"></div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-save"></i>
                    <span wire:loading.remove wire:target="save">Save Trip</span>
                    <span wire:loading wire:target="save"><i class="fas fa-spinner fa-spin"></i> Saving...</span>
                </button>
                <a href="{{ $editing ? route('trips.show', $trip) : route('trips.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>

        {{-- Only show services section if editing or after pending trip created--}}
        @if ($editing && $trip && $trip->id)
        <div x-data="{ activeSection: 'passengers' }" class="space-y-4">
            {{-- Section Tabs --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="flex border-b border-gray-200 overflow-x-auto">
                <button type="button" @click="activeSection = 'passengers'" :class="activeSection === 'passengers' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium whitespace-nowrap border-b-2 transition-colors"><i class="fas fa-users mr-1"></i> Passengers ({{ $trip->passengers->count() }})</button>
                    <button type="button" @click="activeSection = 'flights'" :class="activeSection === 'flights' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium whitespace-nowrap border-b-2 transition-colors"><i class="fas fa-plane mr-1"></i> Flights ({{ $trip->flightSegments->count() }})</button>
                    <button type="button" @click="activeSection = 'hotels'" :class="activeSection === 'hotels' ? 'border-emerald-600 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium whitespace-nowrap border-b-2 transition-colors"><i class="fas fa-hotel mr-1"></i> Hotels ({{ $trip->hotelBookings->count() }})</button>
                    <button type="button" @click="activeSection = 'transfers'" :class="activeSection === 'transfers' ? 'border-orange-600 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium whitespace-nowrap border-b-2 transition-colors"><i class="fas fa-shuttle-van mr-1"></i> Transfers ({{ $trip->transferBookings->count() }})</button>
                    <button type="button" @click="activeSection = 'visas'" :class="activeSection === 'visas' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium whitespace-nowrap border-b-2 transition-colors"><i class="fas fa-passport mr-1"></i> Visas ({{ $trip->visaApplications->count() }})</button>
                    <button type="button" @click="activeSection = 'insurance'" :class="activeSection === 'insurance' ? 'border-pink-600 text-pink-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium whitespace-nowrap border-b-2 transition-colors"><i class="fas fa-shield-alt mr-1"></i> Insurance ({{ $trip->insurancePolicies->count() }})</button>
                    <button type="button" @click="activeSection = 'activities'" :class="activeSection === 'activities' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium whitespace-nowrap border-b-2 transition-colors"><i class="fas fa-ticket-alt mr-1"></i> Activities ({{ $trip->activities->count() }})</button>
                    <button type="button" @click="activeSection = 'cruises'" :class="activeSection === 'cruises' ? 'border-sky-600 text-sky-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium whitespace-nowrap border-b-2 transition-colors"><i class="fas fa-ship mr-1"></i> Cruises ({{ $trip->cruiseBookings->count() }})</button>
                    <button type="button" @click="activeSection = 'trains'" :class="activeSection === 'trains' ? 'border-stone-600 text-stone-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium whitespace-nowrap border-b-2 transition-colors"><i class="fas fa-train mr-1"></i> Trains ({{ $trip->trainBookings->count() }})</button>
                    <button type="button" @click="activeSection = 'cars'" :class="activeSection === 'cars' ? 'border-yellow-600 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium whitespace-nowrap border-b-2 transition-colors"><i class="fas fa-car mr-1"></i> Cars ({{ $trip->carRentals->count() }})</button>
                    <button type="button" @click="activeSection = 'packages'" :class="activeSection === 'packages' ? 'border-teal-600 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium whitespace-nowrap border-b-2 transition-colors"><i class="fas fa-box mr-1"></i> Packages ({{ $trip->packageBookings->count() }})</button>
                    <button type="button" @click="activeSection = 'other'" :class="activeSection === 'other' ? 'border-gray-600 text-gray-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-3 py-2 text-xs font-medium whitespace-nowrap border-b-2 transition-colors"><i class="fas fa-ellipsis-h mr-1"></i> Other ({{ $trip->otherServices->count() }})</button>
                </div>
            </div>

            {{-- PASSENGERS --}}
            <div x-show="activeSection === 'passengers'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-users text-blue-500 mr-2"></i> Passengers</h3>
                    <button wire:click="openPassengerForm" class="text-xs text-blue-600 hover:underline font-medium">
                        <i class="fas fa-plus"></i> Add Passenger
                    </button>
                </div>
                <div class="p-4">
                    @if ($trip->passengers->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach ($trip->passengers as $p)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center shrink-0 text-white text-xs font-bold">
                                    {{ substr($p->first_name, 0, 1) }}{{ substr($p->last_name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $p->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $p->nationality ?: '—' }}{{ $p->passport_number ? ' · '.$p->passport_number : '' }}</p>
                                </div>
                            </div>
                            <div class="flex gap-1 shrink-0">
                                <button type="button" wire:click="editPassenger('{{ $p->id }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded" title="Edit"><i class="fas fa-edit"></i></button>
                                <button type="button" wire:click="deletePassenger('{{ $p->id }}')" wire:confirm="Remove {{ $p->full_name }}?" class="p-1.5 text-red-500 hover:bg-red-50 rounded" title="Remove"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-400 text-center py-6">No passengers yet. Click "Add Passenger" to add one.</p>
                    @endif

                    @if ($showPassengerForm)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ $editPassengerId ? 'Edit' : 'Add' }} Passenger</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">First Name *</label>
                                <input type="text" wire:model="p_first_name" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500" placeholder="First name" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Last Name *</label>
                                <input type="text" wire:model="p_last_name" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-500" placeholder="Last name" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Date of Birth</label>
                                <input type="date" wire:model="p_date_of_birth" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Nationality</label>
                                <input type="text" wire:model="p_nationality" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="e.g. Egyptian" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Passport No.</label>
                                <input type="text" wire:model="p_passport_number" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="e.g. AB123456" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Passport Expiry</label>
                                <input type="date" wire:model="p_passport_expiry" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                            </div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" wire:click="savePassenger" class="px-4 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Save</button>
                            <button type="button" @click="activeSection = 'passengers'" wire:click="resetPassengerForm" class="px-4 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- FLIGHTS --}}
            <div x-show="activeSection === 'flights'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-plane text-indigo-500 mr-2"></i> Flights</h3>
                    <button wire:click="openFlightForm" class="text-xs text-blue-600 hover:underline font-medium"><i class="fas fa-plus"></i> Add Flight</button>
                </div>
                <div class="p-4 space-y-2">
                    @if ($trip->flightSegments->isNotEmpty())
                    @foreach ($trip->flightSegments as $f)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0"><i class="fas fa-plane text-indigo-500"></i></div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $f->airline }} {{ $f->flight_number }}</p>
                                <p class="text-xs text-gray-500">{{ $f->departure_airport }} → {{ $f->arrival_airport }} · {{ $f->departure_datetime ? $f->departure_datetime->format('d M Y H:i') : '' }}</p>
                            </div>
                            <div class="text-right text-sm">
                                <p class="font-mono font-semibold text-gray-900">{{ number_format($f->selling_price, 2) }}</p>
                                <p class="text-xs text-gray-400">{{ $f->currency }}</p>
                            </div>
                        </div>
                        <div class="flex gap-1 ml-4 shrink-0">
                            <button type="button" wire:click="editFlight('{{ $f->id }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded" title="Edit"><i class="fas fa-edit"></i></button>
                            <button type="button" wire:click="deleteFlight('{{ $f->id }}')" wire:confirm="Delete flight?" class="p-1.5 text-red-500 hover:bg-red-50 rounded" title="Delete"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-sm text-gray-400 text-center py-6">No flights added yet.</p>
                    @endif

                    @if ($showFlightForm)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ $editFlightId ? 'Edit' : 'Add' }} Flight</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Airline</label>
                                <input type="text" wire:model="f_airline" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="e.g. Turkish Airlines" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Flight #</label>
                                <input type="text" wire:model="f_flight_number" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="TK1234" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Departure</label>
                                <input type="text" wire:model="f_departure_airport" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="e.g. TLV" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Arrival</label>
                                <input type="text" wire:model="f_arrival_airport" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="e.g. SSH" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Departure</label>
                                <input type="datetime-local" wire:model="f_departure_datetime" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Arrival</label>
                                <input type="datetime-local" wire:model="f_arrival_datetime" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Selling Price</label>
                                <input type="number" step="0.01" wire:model="f_selling_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Cost Price</label>
                                <input type="number" step="0.01" wire:model="f_cost_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Currency</label>
                                <select wire:model="f_currency" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Status</label>
                                <select wire:model="f_status" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option>confirmed</option><option>pending</option><option>cancelled</option></select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Supplier</label>
                                <select wire:model="f_supplier_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                    <option value="">None</option>
                                    @foreach ($suppliers as $s)
<option value="{{ $s->id }}">{{ $s->company_name }}</option>
                                     @endforeach
                                 </select>
                             </div>
                         </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" wire:click="saveFlight" class="px-4 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Save Flight</button>
                            <button type="button" wire:click="resetFlightForm" class="px-4 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- HOTELS --}}
            <div x-show="activeSection === 'hotels'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-hotel text-emerald-500 mr-2"></i> Hotels</h3>
                    <button wire:click="openHotelForm" class="text-xs text-blue-600 hover:underline font-medium"><i class="fas fa-plus"></i> Add Hotel</button>
                </div>
                <div class="p-4 space-y-2">
                    @if ($trip->hotelBookings->isNotEmpty())
                    @foreach ($trip->hotelBookings as $h)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0"><i class="fas fa-hotel text-emerald-500"></i></div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $h->hotel_name }}</p>
                                <p class="text-xs text-gray-500">{{ $h->city }} · {{ $h->room_type }} x{{ $h->number_of_rooms }} · {{ $h->check_in?->format('d M') }} → {{ $h->check_out?->format('d M') }}</p>
                            </div>
                            <div class="text-right text-sm">
                                <p class="font-mono font-semibold text-gray-900">{{ number_format($h->selling_price, 2) }}</p>
                                <p class="text-xs text-gray-400">{{ $h->currency }}</p>
                            </div>
                        </div>
                        <div class="flex gap-1 ml-4 shrink-0">
                            <button type="button" wire:click="editHotel('{{ $h->id }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><i class="fas fa-edit"></i></button>
                            <button type="button" wire:click="deleteHotel('{{ $h->id }}')" wire:confirm="Delete hotel?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-sm text-gray-400 text-center py-6">No hotels added yet.</p>
                    @endif

                    @if ($showHotelForm)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ $editHotelId ? 'Edit' : 'Add' }} Hotel</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div><label class="block text-xs text-gray-600 mb-1">Hotel Name *</label><input type="text" wire:model="h_hotel_name" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="e.g. Hilton" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">City</label><input type="text" wire:model="h_city" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Room Type</label><input type="text" wire:model="h_room_type" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="e.g. Deluxe" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Rooms</label><input type="number" min="1" wire:model="h_number_of_rooms" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Check In</label><input type="date" wire:model="h_check_in" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Check Out</label><input type="date" wire:model="h_check_out" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="h_selling_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="h_cost_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Currency</label><select wire:model="h_currency" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Status</label><select wire:model="h_status" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option>confirmed</option><option>pending</option><option>cancelled</option></select></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Supplier</label><select wire:model="h_supplier_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option value="">None</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" wire:click="saveHotel" class="px-4 py-1.5 text-xs font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Save Hotel</button>
                            <button type="button" wire:click="resetHotelForm" class="px-4 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- TRANSFERS --}}
            <div x-show="activeSection === 'transfers'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-shuttle-van text-orange-500 mr-2"></i> Transfers</h3>
                    <button wire:click="openTransferForm" class="text-xs text-blue-600 hover:underline font-medium"><i class="fas fa-plus"></i> Add Transfer</button>
                </div>
                <div class="p-4 space-y-2">
                    @if ($trip->transferBookings->isNotEmpty())
                    @foreach ($trip->transferBookings as $tr)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-9 h-9 rounded-lg bg-orange-50 flex items-center justify-center shrink-0"><i class="fas fa-shuttle-van text-orange-500"></i></div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $tr->pickup_location }} → {{ $tr->dropoff_location }}</p>
                                <p class="text-xs text-gray-500">{{ $tr->vehicle_type }} · {{ $tr->passengers }} pax · {{ $tr->pickup_datetime ? $tr->pickup_datetime->format('d M Y H:i') : '' }}</p>
                            </div>
                            <div class="text-right text-sm">
                                <p class="font-mono font-semibold text-gray-900">{{ number_format($tr->selling_price, 2) }}</p>
                            </div>
                        </div>
                        <div class="flex gap-1 ml-4 shrink-0">
                            <button type="button" wire:click="editTransfer('{{ $tr->id }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><i class="fas fa-edit"></i></button>
                            <button type="button" wire:click="deleteTransfer('{{ $tr->id }}')" wire:confirm="Delete transfer?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-sm text-gray-400 text-center py-6">No transfers added yet.</p>
                    @endif

                    @if ($showTransferForm)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ $editTransferId ? 'Edit' : 'Add' }} Transfer</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div><label class="block text-xs text-gray-600 mb-1">Pickup location</label><input type="text" wire:model="t_pickup" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Dropoff location</label><input type="text" wire:model="t_dropoff" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Vehicle type</label><input type="text" wire:model="t_vehicle_type" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="Sedan/Bus/MiniBus" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Pax</label><input type="number" min="1" wire:model="t_passengers" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Pickup Date/Time</label><input type="datetime-local" wire:model="t_pickup_datetime" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="t_selling_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="t_cost_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Currency</label><select wire:model="t_currency" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Supplier</label><select wire:model="t_supplier_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option value="">None</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" wire:click="saveTransfer" class="px-4 py-1.5 text-xs font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700">Save Transfer</button>
                            <button type="button" wire:click="resetTransferForm" class="px-4 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- VISAS --}}
            <div x-show="activeSection === 'visas'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-passport text-purple-500 mr-2"></i> Visas</h3>
                    <button wire:click="openVisaForm" class="text-xs text-blue-600 hover:underline font-medium"><i class="fas fa-plus"></i> Add Visa</button>
                </div>
                <div class="p-4 space-y-2">
                    @if ($trip->visaApplications->isNotEmpty())
                    @foreach ($trip->visaApplications as $v)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center shrink-0"><i class="fas fa-passport text-purple-500"></i></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $v->country }}</p>
                                <p class="text-xs text-gray-500">{{ str_replace('_', ' ', ucfirst($v->visa_type)) }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($v->selling_price, 2) }}</p>
                            <button type="button" wire:click="editVisa('{{ $v->id }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><i class="fas fa-edit"></i></button>
                            <button type="button" wire:click="deleteVisa('{{ $v->id }}')" wire:confirm="Delete visa?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-sm text-gray-400 text-center py-6">No visas added yet.</p>
                    @endif

                    @if ($showVisaForm)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ $editVisaId ? 'Edit' : 'Add' }} Visa</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div><label class="block text-xs text-gray-600 mb-1">Country</label><input type="text" wire:model="v_country" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Visa Type</label><select wire:model="v_visa_type" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option>tourist</option><option>business</option><option>transit</option><option>e-visa</option><option>on_arrival</option></select></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="v_selling_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="v_cost_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Currency</label><select wire:model="v_currency" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" wire:click="saveVisa" class="px-4 py-1.5 text-xs font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700">Save Visa</button>
                            <button type="button" wire:click="resetVisaForm" class="px-4 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- INSURANCE --}}
            <div x-show="activeSection === 'insurance'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-shield-alt text-pink-500 mr-2"></i> Insurance</h3>
                    <button wire:click="openInsuranceForm" class="text-xs text-blue-600 hover:underline font-medium"><i class="fas fa-plus"></i> Add Insurance</button>
                </div>
                <div class="p-4 space-y-2">
                    @if ($trip->insurancePolicies->isNotEmpty())
                    @foreach ($trip->insurancePolicies as $ins)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-pink-50 flex items-center justify-center shrink-0"><i class="fas fa-shield-alt text-pink-500"></i></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $ins->policy_number ?: 'Insurance Policy' }}</p>
                                <p class="text-xs text-gray-500">{{ str_replace('_', ' ', ucfirst($ins->type)) }} · {{ $ins->start_date?->format('d M') }} → {{ $ins->end_date?->format('d M') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($ins->selling_price, 2) }}</p>
                            <button type="button" wire:click="editInsurance('{{ $ins->id }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><i class="fas fa-edit"></i></button>
                            <button type="button" wire:click="deleteInsurance('{{ $ins->id }}')" wire:confirm="Delete insurance?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-sm text-gray-400 text-center py-6">No insurance added yet.</p>
                    @endif

                    @if ($showInsuranceForm)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ $editInsuranceId ? 'Edit' : 'Add' }} Insurance</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div><label class="block text-xs text-gray-600 mb-1">Type</label><select wire:model="i_type" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option>travel</option><option>medical</option><option>cancellation</option><option>baggage</option></select></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Policy #</label><input type="text" wire:model="i_policy_number" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Start Date</label><input type="date" wire:model="i_start_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">End Date</label><input type="date" wire:model="i_end_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="i_selling_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="i_cost_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Currency</label><select wire:model="i_currency" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" wire:click="saveInsurance" class="px-4 py-1.5 text-xs font-medium text-white bg-pink-600 rounded-lg hover:bg-pink-700">Save Insurance</button>
                            <button type="button" wire:click="resetInsuranceForm" class="px-4 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ACTIVITIES --}}
            <div x-show="activeSection === 'activities'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-ticket-alt text-cyan-500 mr-2"></i> Activities</h3>
                    <button wire:click="openActivityForm" class="text-xs text-blue-600 hover:underline font-medium"><i class="fas fa-plus"></i> Add Activity</button>
                </div>
                <div class="p-4 space-y-2">
                    @if ($trip->activities->isNotEmpty())
                    @foreach ($trip->activities as $a)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-cyan-50 flex items-center justify-center shrink-0"><i class="fas fa-ticket-alt text-cyan-500"></i></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $a->name }}</p>
                                <p class="text-xs text-gray-500">{{ $a->location ?: '' }}{{ $a->date ? ($a->location ? ' · ' : '') . $a->date->format('d M Y') : '' }}{{ $a->time ? ' '.$a->time->format('H:i') : '' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($a->selling_price, 2) }}</p>
                            <button type="button" wire:click="editActivity('{{ $a->id }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><i class="fas fa-edit"></i></button>
                            <button type="button" wire:click="deleteActivity('{{ $a->id }}')" wire:confirm="Delete activity?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-sm text-gray-400 text-center py-6">No activities added yet.</p>
                    @endif

                    @if ($showActivityForm)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ $editActivityId ? 'Edit' : 'Add' }} Activity</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div><label class="block text-xs text-gray-600 mb-1">Activity Name</label><input type="text" wire:model="a_name" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Location</label><input type="text" wire:model="a_location" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Date</label><input type="date" wire:model="a_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Time</label><input type="time" wire:model="a_time" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="a_selling_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="a_cost_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Currency</label><select wire:model="a_currency" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" wire:click="saveActivity" class="px-4 py-1.5 text-xs font-medium text-white bg-cyan-600 rounded-lg hover:bg-cyan-700">Save Activity</button>
                            <button type="button" wire:click="resetActivityForm" class="px-4 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- CRUISES --}}
            <div x-show="activeSection === 'cruises'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-ship text-sky-500 mr-2"></i> Cruises</h3>
                    <button wire:click="openCruiseForm" class="text-xs text-blue-600 hover:underline font-medium"><i class="fas fa-plus"></i> Add Cruise</button>
                </div>
                <div class="p-4 space-y-2">
                    @if ($trip->cruiseBookings->isNotEmpty())
                    @foreach ($trip->cruiseBookings as $c)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-sky-50 flex items-center justify-center shrink-0"><i class="fas fa-ship text-sky-500"></i></div>
                            <div><p class="text-sm font-medium text-gray-900">{{ $c->cruise_line ?: 'Cruise' }}{{ $c->ship_name ? ' - '.$c->ship_name : '' }}</p><p class="text-xs text-gray-500">{{ $c->departure_port ?: '' }}{{ $c->departure_date ? ($c->departure_port ? ' · ' : '') . $c->departure_date->format('d M Y') : '' }}</p></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($c->selling_price, 2) }}</p>
                            <button type="button" wire:click="editCruise('{{ $c->id }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><i class="fas fa-edit"></i></button>
                            <button type="button" wire:click="deleteCruise('{{ $c->id }}')" wire:confirm="Delete cruise?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-sm text-gray-400 text-center py-6">No cruises added yet.</p>
                    @endif
                    @if ($showCruiseForm)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ $editCruiseId ? 'Edit' : 'Add' }} Cruise</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div><label class="block text-xs text-gray-600 mb-1">Cruise Line</label><input type="text" wire:model="cr_cruise_line" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Ship Name</label><input type="text" wire:model="cr_ship_name" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Cabin Type</label><input type="text" wire:model="cr_cabin_type" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Departure Port</label><input type="text" wire:model="cr_departure_port" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Arrival Port</label><input type="text" wire:model="cr_arrival_port" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Departure Date</label><input type="date" wire:model="cr_departure_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Arrival Date</label><input type="date" wire:model="cr_arrival_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Itinerary</label><input type="text" wire:model="cr_itinerary" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="cr_selling_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="cr_cost_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Currency</label><select wire:model="cr_currency" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Supplier</label><select wire:model="cr_supplier_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option value="">None</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" wire:click="saveCruise" class="px-4 py-1.5 text-xs font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700">Save Cruise</button>
                            <button type="button" wire:click="resetCruiseForm" class="px-4 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- TRAINS --}}
            <div x-show="activeSection === 'trains'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-train text-stone-500 mr-2"></i> Trains</h3>
                    <button wire:click="openTrainForm" class="text-xs text-blue-600 hover:underline font-medium"><i class="fas fa-plus"></i> Add Train</button>
                </div>
                <div class="p-4 space-y-2">
                    @if ($trip->trainBookings->isNotEmpty())
                    @foreach ($trip->trainBookings as $tr)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-stone-50 flex items-center justify-center shrink-0"><i class="fas fa-train text-stone-500"></i></div>
                            <div><p class="text-sm font-medium text-gray-900">{{ $tr->train_company ?: 'Train' }}{{ $tr->train_number ? ' #'.$tr->train_number : '' }}</p><p class="text-xs text-gray-500">{{ $tr->departure_station ?: '' }}{{ $tr->departure_datetime ? ($tr->departure_station ? ' · ' : '') . $tr->departure_datetime->format('d M Y H:i') : '' }}</p></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($tr->selling_price, 2) }}</p>
                            <button type="button" wire:click="editTrain('{{ $tr->id }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><i class="fas fa-edit"></i></button>
                            <button type="button" wire:click="deleteTrain('{{ $tr->id }}')" wire:confirm="Delete train?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-sm text-gray-400 text-center py-6">No trains added yet.</p>
                    @endif
                    @if ($showTrainForm)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ $editTrainId ? 'Edit' : 'Add' }} Train</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div><label class="block text-xs text-gray-600 mb-1">Company</label><input type="text" wire:model="tr_company" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Train #</label><input type="text" wire:model="tr_train_number" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Departure Station</label><input type="text" wire:model="tr_departure_station" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Arrival Station</label><input type="text" wire:model="tr_arrival_station" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Departure</label><input type="datetime-local" wire:model="tr_departure_datetime" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Arrival</label><input type="datetime-local" wire:model="tr_arrival_datetime" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Class</label><input type="text" wire:model="tr_class" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="tr_selling_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="tr_cost_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Currency</label><select wire:model="tr_currency" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Supplier</label><select wire:model="tr_supplier_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option value="">None</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" wire:click="saveTrain" class="px-4 py-1.5 text-xs font-medium text-white bg-stone-600 rounded-lg hover:bg-stone-700">Save Train</button>
                            <button type="button" wire:click="resetTrainForm" class="px-4 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- CAR RENTALS --}}
            <div x-show="activeSection === 'cars'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-car text-yellow-600 mr-2"></i> Car Rentals</h3>
                    <button wire:click="openCarForm" class="text-xs text-blue-600 hover:underline font-medium"><i class="fas fa-plus"></i> Add Car Rental</button>
                </div>
                <div class="p-4 space-y-2">
                    @if ($trip->carRentals->isNotEmpty())
                    @foreach ($trip->carRentals as $ca)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-yellow-50 flex items-center justify-center shrink-0"><i class="fas fa-car text-yellow-600"></i></div>
                            <div><p class="text-sm font-medium text-gray-900">{{ $ca->company ?: 'Car Rental' }}{{ $ca->car_type ? ' - '.$ca->car_type : '' }}</p><p class="text-xs text-gray-500">{{ $ca->pickup_location ?: '' }}{{ $ca->pickup_datetime ? ($ca->pickup_location ? ' · ' : '') . $ca->pickup_datetime->format('d M Y H:i') : '' }}</p></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($ca->selling_price, 2) }}</p>
                            <button type="button" wire:click="editCar('{{ $ca->id }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><i class="fas fa-edit"></i></button>
                            <button type="button" wire:click="deleteCar('{{ $ca->id }}')" wire:confirm="Delete car rental?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-sm text-gray-400 text-center py-6">No car rentals added yet.</p>
                    @endif
                    @if ($showCarForm)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ $editCarId ? 'Edit' : 'Add' }} Car Rental</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div><label class="block text-xs text-gray-600 mb-1">Company</label><input type="text" wire:model="ca_company" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Car Type</label><input type="text" wire:model="ca_car_type" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Pickup Location</label><input type="text" wire:model="ca_pickup_location" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Dropoff Location</label><input type="text" wire:model="ca_dropoff_location" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Pickup</label><input type="datetime-local" wire:model="ca_pickup_datetime" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Dropoff</label><input type="datetime-local" wire:model="ca_dropoff_datetime" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="ca_selling_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="ca_cost_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Currency</label><select wire:model="ca_currency" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Supplier</label><select wire:model="ca_supplier_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option value="">None</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" wire:click="saveCar" class="px-4 py-1.5 text-xs font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700">Save Car Rental</button>
                            <button type="button" wire:click="resetCarForm" class="px-4 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- PACKAGES --}}
            <div x-show="activeSection === 'packages'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-box text-teal-500 mr-2"></i> Packages</h3>
                    <button wire:click="openPackageForm" class="text-xs text-blue-600 hover:underline font-medium"><i class="fas fa-plus"></i> Add Package</button>
                </div>
                <div class="p-4 space-y-2">
                    @if ($trip->packageBookings->isNotEmpty())
                    @foreach ($trip->packageBookings as $pk)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-teal-50 flex items-center justify-center shrink-0"><i class="fas fa-box text-teal-500"></i></div>
                            <div><p class="text-sm font-medium text-gray-900">{{ $pk->package_name ?: 'Package' }}</p><p class="text-xs text-gray-500">{{ $pk->package_type ?: '' }}{{ $pk->start_date ? ($pk->package_type ? ' · ' : '') . $pk->start_date->format('d M Y') : '' }}</p></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($pk->selling_price, 2) }}</p>
                            <button type="button" wire:click="editPackage('{{ $pk->id }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><i class="fas fa-edit"></i></button>
                            <button type="button" wire:click="deletePackage('{{ $pk->id }}')" wire:confirm="Delete package?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-sm text-gray-400 text-center py-6">No packages added yet.</p>
                    @endif
                    @if ($showPackageForm)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ $editPackageId ? 'Edit' : 'Add' }} Package</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div><label class="block text-xs text-gray-600 mb-1">Package Name</label><input type="text" wire:model="pk_name" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Type</label><input type="text" wire:model="pk_type" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Start Date</label><input type="date" wire:model="pk_start_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">End Date</label><input type="date" wire:model="pk_end_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="pk_selling_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="pk_cost_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Currency</label><select wire:model="pk_currency" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Supplier</label><select wire:model="pk_supplier_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option value="">None</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" wire:click="savePackage" class="px-4 py-1.5 text-xs font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700">Save Package</button>
                            <button type="button" wire:click="resetPackageForm" class="px-4 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- OTHER --}}
            <div x-show="activeSection === 'other'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-ellipsis-h text-gray-500 mr-2"></i> Other Services</h3>
                    <button wire:click="openOtherForm" class="text-xs text-blue-600 hover:underline font-medium"><i class="fas fa-plus"></i> Add Service</button>
                </div>
                <div class="p-4 space-y-2">
                    @if ($trip->otherServices->isNotEmpty())
                    @foreach ($trip->otherServices as $o)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0"><i class="fas fa-ellipsis-h text-gray-500"></i></div>
                            <div><p class="text-sm font-medium text-gray-900">{{ $o->service_name ?: 'Other Service' }}</p><p class="text-xs text-gray-500">{{ $o->service_type ?: '' }}{{ $o->service_date ? ($o->service_type ? ' · ' : '') . $o->service_date->format('d M Y') : '' }}</p></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($o->selling_price, 2) }}</p>
                            <button type="button" wire:click="editOther('{{ $o->id }}')" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><i class="fas fa-edit"></i></button>
                            <button type="button" wire:click="deleteOther('{{ $o->id }}')" wire:confirm="Delete service?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-sm text-gray-400 text-center py-6">No other services added yet.</p>
                    @endif
                    @if ($showOtherForm)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">{{ $editOtherId ? 'Edit' : 'Add' }} Service</h4>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div><label class="block text-xs text-gray-600 mb-1">Service Name</label><input type="text" wire:model="o_name" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Type</label><input type="text" wire:model="o_type" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Date</label><input type="date" wire:model="o_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="o_selling_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="o_cost_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Currency</label><select wire:model="o_currency" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                            <div><label class="block text-xs text-gray-600 mb-1">Supplier</label><select wire:model="o_supplier_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"><option value="">None</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
                        </div>
                        <div class="flex gap-2 mt-3">
                            <button type="button" wire:click="saveOther" class="px-4 py-1.5 text-xs font-medium text-white bg-gray-600 rounded-lg hover:bg-gray-700">Save Service</button>
                            <button type="button" wire:click="resetOtherForm" class="px-4 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- Quick Customer Modal --}}
        @if ($showQuickCustomer)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" wire:click.self="$set('showQuickCustomer', false)">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900">Quick Add Customer</h3>
                    <button type="button" wire:click="$set('showQuickCustomer', false)" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <div class="p-4 space-y-3">
                    <div><label class="block text-xs font-medium text-gray-600 mb-0.5">Customer Name *</label><input type="text" wire:model="quickCustomerName" placeholder="e.g. Ahmed Hassan" class="w-full border border-gray-300 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-blue-500" />
                        @error('quickCustomerName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-0.5">Phone</label><input type="text" wire:model="quickCustomerPhone" placeholder="e.g. +972 50 123 4567" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" /></div>
                    <div><label class="block text-xs font-medium text-gray-600 mb-0.5">Email</label><input type="email" wire:model="quickCustomerEmail" placeholder="e.g. ahmed@example.com" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" /></div>
                    <div class="flex justify-end gap-3">
                        <button type="button" wire:click="$set('showQuickCustomer', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                        <button type="button" wire:click="quickCustomerSave" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Create & Select</button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
function initFormMap() {
    const container = document.getElementById('trip-form-map');
    if (!container || container._leaflet_map) return;

    const lat = parseFloat(@json($latitude)) || 31.5;
    const lng = parseFloat(@json($longitude)) || 34.5;

    let map = L.map(container, { zoomControl: true }).setView([lat, lng], lat === 31.5 && lng === 34.5 ? 8 : 12);
    container._leaflet_map = map;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    let marker;

    function placePin(lat, lng) {
        if (marker) marker.setLatLng([lat, lng]);
        else {
            marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            marker.on('dragend', function () {
                const pos = marker.getLatLng();
                @this.set('latitude', pos.lat.toFixed(6));
                @this.set('longitude', pos.lng.toFixed(6));
            });
        }
        container._leaflet_marker = marker;
        @this.set('latitude', lat.toFixed(6));
        @this.set('longitude', lng.toFixed(6));
    }

    if (parseFloat(@json($latitude)) && parseFloat(@json($longitude))) {
        placePin(lat, lng);
    }

    map.on('click', function (e) {
        placePin(e.latlng.lat, e.latlng.lng);
    });

    setTimeout(() => map.invalidateSize(), 100);
}

document.addEventListener('livewire:init', setTimeout.bind(null, initFormMap, 100));
document.addEventListener('livewire:navigated', setTimeout.bind(null, initFormMap, 150));

Livewire.on('coordinatesFetched', ({ lat, lng }) => {
    const container = document.getElementById('trip-form-map');
    if (!container) return;
    if (!container._leaflet_map) { setTimeout(() => initFormMap(), 200); return; }
    const map = container._leaflet_map;
    let marker = container._leaflet_marker;
    const latNum = parseFloat(lat), lngNum = parseFloat(lng);
    if (marker) marker.setLatLng([latNum, lngNum]);
    else {
        marker = L.marker([latNum, lngNum], { draggable: true }).addTo(map);
        marker.on('dragend', function () {
            const pos = marker.getLatLng();
            @this.set('latitude', pos.lat.toFixed(6));
            @this.set('longitude', pos.lng.toFixed(6));
        });
        container._leaflet_marker = marker;
    }
    map.setView([latNum, lngNum], 12);
    @this.set('latitude', lat);
    @this.set('longitude', lng);
});
</script>
@endpush
</content>
