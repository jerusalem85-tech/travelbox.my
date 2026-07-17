<div x-data="{ activeModal: null }">
    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-2xl font-bold">{{ $trip->name ?: 'Untitled Trip' }}</h2>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if($trip->status === 'confirmed') bg-green-400 text-green-900
                            @elseif($trip->status === 'in_progress') bg-blue-400 text-blue-900
                            @elseif($trip->status === 'completed') bg-gray-200 text-gray-800
                            @elseif($trip->status === 'cancelled') bg-red-400 text-red-900
                            @else bg-yellow-300 text-yellow-900 @endif">
                            {{ str_replace('_', ' ', ucfirst($trip->status)) }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-sm rounded-lg text-xs font-mono tracking-wider">{{ $trip->trip_number }}</span>
                    </div>
                    <p class="text-blue-100 text-sm">{{ ucfirst($trip->type) }} · {{ $trip->destination ?: 'No destination' }}</p>
                    <p class="text-blue-100 text-sm mt-1">{{ $trip->start_date?->format('d M Y') }} → {{ $trip->end_date?->format('d M Y') }} @if($trip->customer) · {{ $trip->customer->full_name }} @endif</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="runAutomation" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium rounded-lg transition-colors"><i class="fas fa-magic"></i> Auto</button>
                    <a href="{{ route('pdfs.voucher', $trip) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium rounded-lg transition-colors"><i class="fas fa-file-pdf"></i> Voucher</a>
                    <a href="{{ route('trips.edit', $trip) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium rounded-lg transition-colors"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Edit</a>
                    <a href="{{ route('trips.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium rounded-lg transition-colors"><i class="fas fa-arrow-left"></i> Back</a>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-white/20">
                <button wire:click="sendWhatsApp" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-lg transition-colors"><i class="fab fa-whatsapp"></i> WhatsApp</button>
                <button wire:click="sendEmail" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium rounded-lg transition-colors"><i class="fas fa-envelope"></i> Email</button>
                <a href="{{ route('pdfs.service-summary', $trip) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-500 hover:bg-purple-600 text-white text-xs font-medium rounded-lg transition-colors"><i class="fas fa-calculator"></i> Costs</a>
                <a href="{{ route('trips.itinerary.download', $trip) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-medium rounded-lg transition-colors"><i class="fas fa-map"></i> Itinerary</a>
                <a href="{{ route('trips.flights', $trip) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-500 hover:bg-indigo-600 text-white text-xs font-medium rounded-lg transition-colors"><i class="fas fa-plane"></i> Flights</a>
                <a href="{{ route('trips.hotels', $trip) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-medium rounded-lg transition-colors"><i class="fas fa-hotel"></i> Hotels</a>
            </div>
        </div>

        {{-- TABS --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="border-b border-gray-200">
                <nav class="flex overflow-x-auto">
                    <button wire:click="$set('activeTab', 'overview')" class="px-4 py-3 text-xs font-medium whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'overview' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-th-large mr-1.5"></i> Overview
                    </button>
                    <button wire:click="$set('activeTab', 'services')" class="px-4 py-3 text-xs font-medium whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'services' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-cogs mr-1.5"></i> Services
                    </button>
                    <button wire:click="$set('activeTab', 'passengers')" class="px-4 py-3 text-xs font-medium whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'passengers' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-users mr-1.5"></i> Passengers
                    </button>
                    <button wire:click="$set('activeTab', 'financial')" class="px-4 py-3 text-xs font-medium whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'financial' ? 'border-emerald-600 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-coins mr-1.5"></i> Financial
                    </button>
                    <button wire:click="$set('activeTab', 'payments')" class="px-4 py-3 text-xs font-medium whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'payments' ? 'border-emerald-600 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-hand-holding-usd mr-1.5"></i> Payments
                    </button>
                    <button wire:click="$set('activeTab', 'docs')" class="px-4 py-3 text-xs font-medium whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'docs' ? 'border-amber-600 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-file-alt mr-1.5"></i> Documents
                    </button>
                    <button wire:click="$set('activeTab', 'tasks')" class="px-4 py-3 text-xs font-medium whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'tasks' ? 'border-cyan-600 text-cyan-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-tasks mr-1.5"></i> Tasks
                    </button>
                    <button wire:click="$set('activeTab', 'itinerary')" class="px-4 py-3 text-xs font-medium whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'itinerary' ? 'border-rose-600 text-rose-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-calendar-day mr-1.5"></i> Itinerary
                    </button>
                    <button wire:click="$set('activeTab', 'timeline')" class="px-4 py-3 text-xs font-medium whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'timeline' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-history mr-1.5"></i> Timeline
                    </button>
                    <button wire:click="$set('activeTab', 'notes')" class="px-4 py-3 text-xs font-medium whitespace-nowrap border-b-2 transition-colors {{ $activeTab === 'notes' ? 'border-gray-600 text-gray-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-sticky-note mr-1.5"></i> Notes
                    </button>
                </nav>
            </div>
        </div>

        {{-- TAB: OVERVIEW --}}
        @if($activeTab === 'overview')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                {{-- Trip Quick Summary --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="bg-white rounded-xl border border-gray-200 p-4"><p class="text-xs text-gray-500">Status</p><p class="text-sm font-semibold mt-1 capitalize">{{ str_replace('_', ' ', $trip->status) }}</p></div>
                    <div class="bg-white rounded-xl border border-gray-200 p-4"><p class="text-xs text-gray-500">Passengers</p><p class="text-sm font-semibold mt-1">{{ $trip->passengers->count() }}</p></div>
                    <div class="bg-white rounded-xl border border-gray-200 p-4"><p class="text-xs text-gray-500">Services</p><p class="text-sm font-semibold mt-1">{{ $trip->flightSegments->count() + $trip->hotelBookings->count() + $trip->transferBookings->count() + $trip->visaApplications->count() + $trip->insurancePolicies->count() + $trip->activities->count() + $trip->cruiseBookings->count() + $trip->trainBookings->count() + $trip->carRentals->count() + $trip->packageBookings->count() + $trip->otherServices->count() }}</p></div>
                    <div class="bg-white rounded-xl border border-gray-200 p-4"><p class="text-xs text-gray-500">Profit</p><p class="text-sm font-semibold mt-1 {{ $trip->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($trip->profit, 2) }}</p></div>
                </div>

                {{-- Quick Timeline - Last 5 events --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-t-4 border-t-purple-400">
                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-history text-purple-500 mr-2"></i> Recent Activity</h3>
                        <button wire:click="$set('activeTab', 'timeline')" class="text-xs text-purple-600 hover:underline">View all</button>
                    </div>
                    <div class="p-4">
                        @php $events = $trip->timeline()->latest()->take(5)->get(); @endphp
                        @if ($events->isNotEmpty())
                        <div class="relative pl-5 space-y-3">
                            <div class="absolute left-2 top-2 bottom-2 w-px bg-gray-200"></div>
                            @foreach ($events as $entry)
                            <div class="relative">
                                <div class="absolute -left-3.5 top-1 w-2 h-2 rounded-full
                                    @if(in_array($entry->type, ['trip_created','service_added'])) bg-blue-500
                                    @elseif(in_array($entry->type, ['payment_received','invoice_created'])) bg-green-500
                                    @elseif(str_contains($entry->type, 'passenger')) bg-amber-500
                                    @elseif(str_contains($entry->type, 'deleted')) bg-red-500
                                    @else bg-gray-400 @endif">
                                </div>
                                <p class="text-xs text-gray-900">{{ $entry->description }}</p>
                                <p class="text-xs text-gray-400">{{ $entry->created_at->diffForHumans() }}</p>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-400 text-center py-4">No activity yet.</p>
                        @endif
                    </div>
                </div>

                {{-- Service Mix Mini --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-t-4 border-t-blue-400">
                    <div class="px-4 py-3 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-cogs text-blue-500 mr-2"></i> Services at a Glance</h3></div>
                    <div class="p-4">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            @foreach([
                                ['icon' => 'fa-plane text-indigo-500', 'label' => 'Flights', 'count' => $trip->flightSegments->count(), 'color' => 'indigo'],
                                ['icon' => 'fa-hotel text-emerald-500', 'label' => 'Hotels', 'count' => $trip->hotelBookings->count(), 'color' => 'emerald'],
                                ['icon' => 'fa-shuttle-van text-orange-500', 'label' => 'Transfers', 'count' => $trip->transferBookings->count(), 'color' => 'orange'],
                                ['icon' => 'fa-passport text-purple-500', 'label' => 'Visas', 'count' => $trip->visaApplications->count(), 'color' => 'purple'],
                                ['icon' => 'fa-shield-alt text-pink-500', 'label' => 'Insurance', 'count' => $trip->insurancePolicies->count(), 'color' => 'pink'],
                                ['icon' => 'fa-ticket-alt text-cyan-500', 'label' => 'Activities', 'count' => $trip->activities->count(), 'color' => 'cyan'],
                                ['icon' => 'fa-ship text-sky-500', 'label' => 'Cruises', 'count' => $trip->cruiseBookings->count(), 'color' => 'sky'],
                                ['icon' => 'fa-train text-stone-500', 'label' => 'Trains', 'count' => $trip->trainBookings->count(), 'color' => 'stone'],
                            ] as $svc)
                            <div class="flex items-center gap-2 p-2.5 bg-gray-50 rounded-lg">
                                <i class="fas {{ $svc['icon'] }}"></i>
                                <div><p class="text-xs font-medium text-gray-900">{{ $svc['count'] }}</p><p class="text-xs text-gray-500">{{ $svc['label'] }}</p></div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div>
                {{-- Financial Summary Sidebar --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-t-4 border-t-emerald-400 sticky top-4">
                    <div class="px-4 py-3 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-coins text-emerald-500 mr-2"></i> Financial Summary</h3></div>
                    <div class="p-4 space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Revenue</span><span class="font-mono font-semibold text-emerald-600">{{ number_format($trip->total_selling_price, 2) }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Cost</span><span class="font-mono">{{ number_format($trip->total_cost_price, 2) }}</span></div>
                        <div class="border-t pt-2 mt-2">
                            <div class="flex justify-between"><span class="font-medium">Profit</span><span class="font-mono font-bold {{ $trip->profit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($trip->profit, 2) }}</span></div>
                            <div class="flex justify-between mt-1"><span class="text-gray-500">Margin</span><span class="font-mono text-xs {{ $trip->profit_margin >= 20 ? 'text-emerald-600' : ($trip->profit_margin >= 10 ? 'text-amber-600' : 'text-red-600') }}">{{ number_format($trip->profit_margin, 1) }}%</span></div>
                        </div>
                        <div class="border-t pt-2 mt-2">
                            <div class="flex justify-between"><span class="text-gray-500">Paid by Customer</span><span class="font-mono text-emerald-600 font-semibold">{{ number_format($this->totalPaid, 2) }}</span></div>
                            <div class="flex justify-between mt-1"><span class="text-gray-500">Balance Due</span><span class="font-mono {{ $this->totalDue > 0 ? 'text-red-600 font-bold' : 'text-emerald-600' }}">{{ number_format($this->totalDue, 2) }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- TAB: SERVICES --}}
        @if($activeTab === 'services')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-4">

                {{-- PASSENGERS --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-users text-blue-500 mr-2"></i> Passengers ({{ $trip->passengers->count() }})</h3>
                        <button wire:click="openPassengerForm" @@click="activeModal = 'passenger'" class="text-xs text-blue-600 hover:underline font-medium"><svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add</button>
                    </div>
                    <div class="p-4">
                        @if ($trip->passengers->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach ($trip->passengers as $p)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border-l-4 border-l-blue-400">
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
                                    <button type="button" wire:click="editPassenger('{{ $p->id }}')" @@click="activeModal = 'passenger'" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded" title="Edit"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                    <button type="button" wire:click="deletePassenger('{{ $p->id }}')" wire:confirm="Remove {{ $p->full_name }}?" class="p-1.5 text-red-500 hover:bg-red-50 rounded" title="Remove"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-50 flex items-center justify-center"><svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                            <p class="text-sm text-gray-400">No passengers added yet</p>
                            <button wire:click="openPassengerForm" @@click="activeModal = 'passenger'" class="mt-2 text-xs text-blue-600 hover:underline">+ Add a passenger</button>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- FLIGHTS --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-plane text-indigo-500 mr-2"></i> Flights ({{ $trip->flightSegments->count() }})</h3>
                        <button wire:click="openFlightForm" @@click="activeModal = 'flight'" class="text-xs text-indigo-600 hover:underline font-medium"><svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add</button>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse ($trip->flightSegments as $f)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border-l-4 border-l-indigo-400">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="w-9 h-9 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0"><i class="fas fa-plane text-indigo-500"></i></div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $f->airline }} {{ $f->flight_number }}</p>
                                    <p class="text-xs text-gray-500">{{ $f->departure_airport }} → {{ $f->arrival_airport }} · {{ $f->departure_datetime ? $f->departure_datetime->format('d M Y H:i') : '' }}</p>
                                </div>
                                <div class="text-right text-sm"><p class="font-mono font-semibold text-gray-900">{{ number_format($f->selling_price, 2) }}</p><p class="text-xs text-gray-400">{{ $f->currency }}</p></div>
                            </div>
                            <div class="flex gap-1 ml-4 shrink-0">
                                <button type="button" wire:click="editFlight('{{ $f->id }}')" @@click="activeModal = 'flight'" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <button type="button" wire:click="deleteFlight('{{ $f->id }}')" wire:confirm="Delete flight?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-lg bg-gray-50 flex items-center justify-center"><svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg></div>
                            <p class="text-sm text-gray-400">No flights added</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- HOTELS --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-hotel text-emerald-500 mr-2"></i> Hotels ({{ $trip->hotelBookings->count() }})</h3>
                        <button wire:click="openHotelForm" @@click="activeModal = 'hotel'" class="text-xs text-emerald-600 hover:underline font-medium"><svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add</button>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse ($trip->hotelBookings as $h)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 border-l-4 border-l-emerald-400">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0"><i class="fas fa-hotel text-emerald-500"></i></div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $h->hotel_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $h->city }} · {{ $h->room_type }} x{{ $h->number_of_rooms }} · {{ $h->check_in?->format('d M') }} → {{ $h->check_out?->format('d M') }}</p>
                                </div>
                                <div class="text-right text-sm"><p class="font-mono font-semibold text-gray-900">{{ number_format($h->selling_price, 2) }}</p><p class="text-xs text-gray-400">{{ $h->currency }}</p></div>
                            </div>
                            <div class="flex gap-1 ml-4 shrink-0">
                                <button type="button" wire:click="editHotel('{{ $h->id }}')" @@click="activeModal = 'hotel'" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <button type="button" wire:click="deleteHotel('{{ $h->id }}')" wire:confirm="Delete hotel?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-lg bg-gray-50 flex items-center justify-center"><svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>
                            <p class="text-sm text-gray-400">No hotels added</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- TRANSFERS --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-shuttle-van text-orange-500 mr-2"></i> Transfers ({{ $trip->transferBookings->count() }})</h3>
                        <button wire:click="openTransferForm" @@click="activeModal = 'transfer'" class="text-xs text-orange-600 hover:underline font-medium"><svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add</button>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse ($trip->transferBookings as $tr)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border-l-4 border-l-orange-400">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="w-9 h-9 rounded-lg bg-orange-50 flex items-center justify-center shrink-0"><i class="fas fa-shuttle-van text-orange-500"></i></div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $tr->pickup_location }} → {{ $tr->dropoff_location }}</p>
                                    <p class="text-xs text-gray-500">{{ $tr->vehicle_type }} · {{ $tr->passengers }} pax · {{ $tr->pickup_datetime ? $tr->pickup_datetime->format('d M Y H:i') : '' }}</p>
                                </div>
                                <div class="text-right text-sm"><p class="font-mono font-semibold text-gray-900">{{ number_format($tr->selling_price, 2) }}</p></div>
                            </div>
                            <div class="flex gap-1 ml-4 shrink-0">
                                <button type="button" wire:click="editTransfer('{{ $tr->id }}')" @@click="activeModal = 'transfer'" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <button type="button" wire:click="deleteTransfer('{{ $tr->id }}')" wire:confirm="Delete transfer?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-lg bg-gray-50 flex items-center justify-center"><svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg></div>
                            <p class="text-sm text-gray-400">No transfers added</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- VISAS --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-passport text-purple-500 mr-2"></i> Visas ({{ $trip->visaApplications->count() }})</h3>
                        <button wire:click="openVisaForm" @@click="activeModal = 'visa'" class="text-xs text-purple-600 hover:underline font-medium"><svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add</button>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse ($trip->visaApplications as $v)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border-l-4 border-l-purple-400">
                            <div class="flex items-center gap-3"><div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center shrink-0"><i class="fas fa-passport text-purple-500"></i></div>
                                <div><p class="text-sm font-medium text-gray-900">{{ $v->country }}</p><p class="text-xs text-gray-500">{{ str_replace('_', ' ', ucfirst($v->visa_type)) }}</p></div>
                            </div>
                            <div class="flex items-center gap-4">
                                <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($v->selling_price, 2) }}</p>
                                <button type="button" wire:click="editVisa('{{ $v->id }}')" @@click="activeModal = 'visa'" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <button type="button" wire:click="deleteVisa('{{ $v->id }}')" wire:confirm="Delete visa?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-lg bg-gray-50 flex items-center justify-center"><svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                            <p class="text-sm text-gray-400">No visas added</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- INSURANCE --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-shield-alt text-pink-500 mr-2"></i> Insurance ({{ $trip->insurancePolicies->count() }})</h3>
                        <button wire:click="openInsuranceForm" @@click="activeModal = 'insurance'" class="text-xs text-pink-600 hover:underline font-medium"><svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add</button>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse ($trip->insurancePolicies as $ins)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border-l-4 border-l-pink-400">
                            <div class="flex items-center gap-3"><div class="w-9 h-9 rounded-lg bg-pink-50 flex items-center justify-center shrink-0"><i class="fas fa-shield-alt text-pink-500"></i></div>
                                <div><p class="text-sm font-medium text-gray-900">{{ $ins->policy_number ?: 'Insurance' }}</p><p class="text-xs text-gray-500">{{ str_replace('_', ' ', ucfirst($ins->type)) }} · {{ $ins->start_date?->format('d M') }} → {{ $ins->end_date?->format('d M') }}</p></div>
                            </div>
                            <div class="flex items-center gap-4">
                                <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($ins->selling_price, 2) }}</p>
                                <button type="button" wire:click="editInsurance('{{ $ins->id }}')" @@click="activeModal = 'insurance'" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <button type="button" wire:click="deleteInsurance('{{ $ins->id }}')" wire:confirm="Delete insurance?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-lg bg-gray-50 flex items-center justify-center"><svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
                            <p class="text-sm text-gray-400">No insurance added</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- ACTIVITIES --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-ticket-alt text-cyan-500 mr-2"></i> Activities ({{ $trip->activities->count() }})</h3>
                        <button wire:click="openActivityForm" @@click="activeModal = 'activity'" class="text-xs text-cyan-600 hover:underline font-medium"><svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add</button>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse ($trip->activities as $a)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border-l-4 border-l-cyan-400">
                            <div class="flex items-center gap-3"><div class="w-9 h-9 rounded-lg bg-cyan-50 flex items-center justify-center shrink-0"><i class="fas fa-ticket-alt text-cyan-500"></i></div>
                                <div><p class="text-sm font-medium text-gray-900">{{ $a->name }}</p><p class="text-xs text-gray-500">{{ $a->location ?: '' }}{{ $a->date ? ($a->location ? ' · ' : '') . $a->date->format('d M Y') : '' }}{{ $a->time ? ' '.$a->time->format('H:i') : '' }}</p></div>
                            </div>
                            <div class="flex items-center gap-4">
                                <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($a->selling_price, 2) }}</p>
                                <button type="button" wire:click="editActivity('{{ $a->id }}')" @@click="activeModal = 'activity'" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <button type="button" wire:click="deleteActivity('{{ $a->id }}')" wire:confirm="Delete activity?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-lg bg-gray-50 flex items-center justify-center"><svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                            <p class="text-sm text-gray-400">No activities added</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- CRUISES --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-ship text-sky-500 mr-2"></i> Cruises ({{ $trip->cruiseBookings->count() }})</h3>
                        <button wire:click="openCruiseForm" @@click="activeModal = 'cruise'" class="text-xs text-sky-600 hover:underline font-medium"><svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add</button>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse ($trip->cruiseBookings as $c)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border-l-4 border-l-sky-400">
                            <div class="flex items-center gap-3"><div class="w-9 h-9 rounded-lg bg-sky-50 flex items-center justify-center shrink-0"><i class="fas fa-ship text-sky-500"></i></div>
                                <div><p class="text-sm font-medium text-gray-900">{{ $c->cruise_line ?: 'Cruise' }}{{ $c->ship_name ? ' - '.$c->ship_name : '' }}</p><p class="text-xs text-gray-500">{{ $c->departure_port ?: '' }}{{ $c->departure_date ? ($c->departure_port ? ' · ' : '') . $c->departure_date->format('d M Y') : '' }}</p></div>
                            </div>
                            <div class="flex items-center gap-4">
                                <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($c->selling_price, 2) }}</p>
                                <button type="button" wire:click="editCruise('{{ $c->id }}')" @@click="activeModal = 'cruise'" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <button type="button" wire:click="deleteCruise('{{ $c->id }}')" wire:confirm="Delete cruise?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-lg bg-gray-50 flex items-center justify-center"><i class="fas fa-ship text-gray-300 text-xl"></i></div>
                            <p class="text-sm text-gray-400">No cruises added</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- TRAINS --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-train text-stone-500 mr-2"></i> Trains ({{ $trip->trainBookings->count() }})</h3>
                        <button wire:click="openTrainForm" @@click="activeModal = 'train'" class="text-xs text-stone-600 hover:underline font-medium"><svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add</button>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse ($trip->trainBookings as $tr)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border-l-4 border-l-stone-400">
                            <div class="flex items-center gap-3"><div class="w-9 h-9 rounded-lg bg-stone-50 flex items-center justify-center shrink-0"><i class="fas fa-train text-stone-500"></i></div>
                                <div><p class="text-sm font-medium text-gray-900">{{ $tr->train_company ?: 'Train' }}{{ $tr->train_number ? ' #'.$tr->train_number : '' }}</p><p class="text-xs text-gray-500">{{ $tr->departure_station ?: '' }}{{ $tr->departure_datetime ? ($tr->departure_station ? ' · ' : '') . $tr->departure_datetime->format('d M Y H:i') : '' }}</p></div>
                            </div>
                            <div class="flex items-center gap-4">
                                <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($tr->selling_price, 2) }}</p>
                                <button type="button" wire:click="editTrain('{{ $tr->id }}')" @@click="activeModal = 'train'" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <button type="button" wire:click="deleteTrain('{{ $tr->id }}')" wire:confirm="Delete train?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-lg bg-gray-50 flex items-center justify-center"><i class="fas fa-train text-gray-300 text-xl"></i></div>
                            <p class="text-sm text-gray-400">No trains added</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- CAR RENTALS --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-car text-yellow-600 mr-2"></i> Car Rentals ({{ $trip->carRentals->count() }})</h3>
                        <button wire:click="openCarForm" @@click="activeModal = 'car'" class="text-xs text-yellow-600 hover:underline font-medium"><svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add</button>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse ($trip->carRentals as $ca)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border-l-4 border-l-yellow-400">
                            <div class="flex items-center gap-3"><div class="w-9 h-9 rounded-lg bg-yellow-50 flex items-center justify-center shrink-0"><i class="fas fa-car text-yellow-600"></i></div>
                                <div><p class="text-sm font-medium text-gray-900">{{ $ca->company ?: 'Car Rental' }}{{ $ca->car_type ? ' - '.$ca->car_type : '' }}</p><p class="text-xs text-gray-500">{{ $ca->pickup_location ?: '' }}{{ $ca->pickup_datetime ? ($ca->pickup_location ? ' · ' : '') . $ca->pickup_datetime->format('d M Y H:i') : '' }}</p></div>
                            </div>
                            <div class="flex items-center gap-4">
                                <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($ca->selling_price, 2) }}</p>
                                <button type="button" wire:click="editCar('{{ $ca->id }}')" @@click="activeModal = 'car'" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <button type="button" wire:click="deleteCar('{{ $ca->id }}')" wire:confirm="Delete car rental?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-lg bg-gray-50 flex items-center justify-center"><i class="fas fa-car text-gray-300 text-xl"></i></div>
                            <p class="text-sm text-gray-400">No car rentals added</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- PACKAGES --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-box text-teal-500 mr-2"></i> Packages ({{ $trip->packageBookings->count() }})</h3>
                        <button wire:click="openPackageForm" @@click="activeModal = 'package'" class="text-xs text-teal-600 hover:underline font-medium"><svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add</button>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse ($trip->packageBookings as $pk)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border-l-4 border-l-teal-400">
                            <div class="flex items-center gap-3"><div class="w-9 h-9 rounded-lg bg-teal-50 flex items-center justify-center shrink-0"><i class="fas fa-box text-teal-500"></i></div>
                                <div><p class="text-sm font-medium text-gray-900">{{ $pk->package_name ?: 'Package' }}</p><p class="text-xs text-gray-500">{{ $pk->package_type ?: '' }}{{ $pk->start_date ? ($pk->package_type ? ' · ' : '') . $pk->start_date->format('d M Y') : '' }}</p></div>
                            </div>
                            <div class="flex items-center gap-4">
                                <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($pk->selling_price, 2) }}</p>
                                <button type="button" wire:click="editPackage('{{ $pk->id }}')" @@click="activeModal = 'package'" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <button type="button" wire:click="deletePackage('{{ $pk->id }}')" wire:confirm="Delete package?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-lg bg-gray-50 flex items-center justify-center"><i class="fas fa-box text-gray-300 text-xl"></i></div>
                            <p class="text-sm text-gray-400">No packages added</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- OTHER SERVICES --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-ellipsis-h text-gray-500 mr-2"></i> Other Services ({{ $trip->otherServices->count() }})</h3>
                        <button wire:click="openOtherForm" @@click="activeModal = 'other'" class="text-xs text-gray-600 hover:underline font-medium"><svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add</button>
                    </div>
                    <div class="p-4 space-y-2">
                        @forelse ($trip->otherServices as $o)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border-l-4 border-l-gray-400">
                            <div class="flex items-center gap-3"><div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center shrink-0"><i class="fas fa-ellipsis-h text-gray-500"></i></div>
                                <div><p class="text-sm font-medium text-gray-900">{{ $o->service_name ?: 'Other Service' }}</p><p class="text-xs text-gray-500">{{ $o->service_type ?: '' }}{{ $o->service_date ? ($o->service_type ? ' · ' : '') . $o->service_date->format('d M Y') : '' }}</p></div>
                            </div>
                            <div class="flex items-center gap-4">
                                <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($o->selling_price, 2) }}</p>
                                <button type="button" wire:click="editOther('{{ $o->id }}')" @@click="activeModal = 'other'" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                <button type="button" wire:click="deleteOther('{{ $o->id }}')" wire:confirm="Delete other service?" class="p-1.5 text-red-500 hover:bg-red-50 rounded"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-lg bg-gray-50 flex items-center justify-center"><i class="fas fa-ellipsis-h text-gray-300 text-xl"></i></div>
                            <p class="text-sm text-gray-400">No other services added</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- Sidebar with Sticky Financial Summary --}}
            <div class="space-y-6">
                <div class="sticky top-4 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-t-4 border-t-emerald-400">
                        <div class="px-5 py-3 border-b border-gray-100"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-coins text-emerald-500 mr-2"></i> Financial Summary</h3></div>
                        <div class="p-5 space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-gray-500">Selling Price</span><span class="font-mono font-semibold text-emerald-600">{{ number_format($trip->total_selling_price, 2) }}</span></div>
                            <div class="flex justify-between"><span class="text-gray-500">Cost Price</span><span class="font-mono">{{ number_format($trip->total_cost_price, 2) }}</span></div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between"><span class="font-medium">Profit</span><span class="font-mono font-bold {{ $trip->profit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($trip->profit, 2) }}</span></div>
                                <div class="flex justify-between mt-1"><span class="text-gray-500">Margin</span><span class="font-mono text-xs {{ $trip->profit_margin >= 20 ? 'text-emerald-600' : ($trip->profit_margin >= 10 ? 'text-amber-600' : 'text-red-600') }}">{{ number_format($trip->profit_margin, 1) }}%</span></div>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between"><span class="text-gray-500">Paid</span><span class="font-mono text-emerald-600 font-semibold">{{ number_format($this->totalPaid, 2) }}</span></div>
                                <div class="flex justify-between mt-1"><span class="text-gray-500">Balance Due</span><span class="font-mono {{ $this->totalDue > 0 ? 'text-red-600 font-bold' : 'text-emerald-600' }}">{{ number_format($this->totalDue, 2) }}</span></div>
                            </div>
                            @if($trip->customer)
                            <div class="border-t pt-2 mt-2"><div class="flex justify-between"><span class="text-gray-500">Customer Balance</span><span class="font-mono {{ ($trip->customer->current_balance ?? 0) > 0 ? 'text-green-600' : 'text-gray-600' }}">{{ number_format($trip->customer->current_balance ?? 0, 2) }}</span></div></div>
                            @endif
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-5 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-list text-gray-500 mr-2"></i> Breakdown</h3></div>
                        <div class="p-4 space-y-2 text-sm">
                            @foreach([
                                ['icon' => 'fa-plane text-indigo-500', 'label' => 'Flights', 'count' => $trip->flightSegments->count()],
                                ['icon' => 'fa-hotel text-emerald-500', 'label' => 'Hotels', 'count' => $trip->hotelBookings->count()],
                                ['icon' => 'fa-shuttle-van text-orange-500', 'label' => 'Transfers', 'count' => $trip->transferBookings->count()],
                                ['icon' => 'fa-passport text-purple-500', 'label' => 'Visas', 'count' => $trip->visaApplications->count()],
                                ['icon' => 'fa-shield-alt text-pink-500', 'label' => 'Insurance', 'count' => $trip->insurancePolicies->count()],
                                ['icon' => 'fa-ticket-alt text-cyan-500', 'label' => 'Activities', 'count' => $trip->activities->count()],
                                ['icon' => 'fa-ship text-sky-500', 'label' => 'Cruises', 'count' => $trip->cruiseBookings->count()],
                                ['icon' => 'fa-train text-stone-500', 'label' => 'Trains', 'count' => $trip->trainBookings->count()],
                                ['icon' => 'fa-car text-yellow-600', 'label' => 'Car Rentals', 'count' => $trip->carRentals->count()],
                                ['icon' => 'fa-box text-teal-500', 'label' => 'Packages', 'count' => $trip->packageBookings->count()],
                                ['icon' => 'fa-ellipsis-h text-gray-500', 'label' => 'Other', 'count' => $trip->otherServices->count()],
                            ] as $item)
                            <div class="flex items-center justify-between"><span class="text-gray-600"><i class="fas {{ $item['icon'] }} mr-2"></i>{{ $item['label'] }}</span><span class="font-medium {{ $item['count'] > 0 ? 'text-gray-900' : 'text-gray-400' }}">{{ $item['count'] }}</span></div>
                            @endforeach
                            <div class="border-t pt-2 mt-2 flex justify-between font-medium"><span>Passengers</span><span>{{ $trip->passengers->count() }}</span></div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-5 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-tasks text-cyan-500 mr-2"></i> Tasks</h3></div>
                        <div class="p-4">@livewire('trips.trip-tasks', ['trip' => $trip], key('tasks-'.$trip->id))</div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-5 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-receipt text-orange-500 mr-2"></i> Expenses ({{ $trip->expenses->count() }})</h3></div>
                        <div class="p-4">@livewire('trips.trip-expenses', ['trip' => $trip], key('expenses-'.$trip->id))</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- TAB: PASSENGERS --}}
        @if($activeTab === 'passengers')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-t-4 border-t-blue-400">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-users text-blue-500 mr-2"></i> Passengers ({{ $trip->passengers->count() }})</h3>
                <button wire:click="openPassengerForm" @@click="activeModal = 'passenger'" class="text-xs text-blue-600 hover:underline font-medium"><svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add</button>
            </div>
            <div class="p-4">
                @if ($trip->passengers->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($trip->passengers as $p)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border-l-4 border-l-blue-400">
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
                            <button type="button" wire:click="editPassenger('{{ $p->id }}')" @@click="activeModal = 'passenger'" class="p-1.5 text-amber-500 hover:bg-amber-50 rounded" title="Edit"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                            <button type="button" wire:click="deletePassenger('{{ $p->id }}')" wire:confirm="Remove {{ $p->full_name }}?" class="p-1.5 text-red-500 hover:bg-red-50 rounded" title="Remove"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-50 flex items-center justify-center"><svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
                    <p class="text-sm text-gray-400">No passengers added yet</p>
                    <button wire:click="openPassengerForm" @@click="activeModal = 'passenger'" class="mt-2 text-xs text-blue-600 hover:underline">+ Add a passenger</button>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- TAB: FINANCIAL --}}
        @if($activeTab === 'finance')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-file-invoice text-gray-500 mr-2"></i> Invoices</h3></div>
                    <div class="p-4">
                        @if($trip->invoices->isNotEmpty())
                        <div class="space-y-2">
                            @foreach($trip->invoices as $inv)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border-l-4 border-l-emerald-400">
                                <div><p class="text-sm font-medium text-gray-900">{{ $inv->invoice_number }}</p><p class="text-xs text-gray-500">{{ number_format($inv->total, 2) }} {{ $inv->currency }} · {{ $inv->created_at->format('d M Y') }}</p></div>
                                <span class="text-xs px-2 py-1 rounded-full {{ $inv->status === 'paid' ? 'bg-green-50 text-green-700' : ($inv->status === 'sent' ? 'bg-blue-50 text-blue-700' : 'bg-gray-50 text-gray-600') }}">{{ ucfirst($inv->status) }}</span>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-400 text-center py-4">No invoices yet.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-calculator text-gray-500 mr-2"></i> Line Item Profit</h3></div>
                    <div class="p-6">
                        @php
                            $items = collect()
                                ->merge($trip->flightSegments->map(fn($s) => ['type' => 'Flights', 'name' => $s->airline.' '.$s->flight_number, 'selling' => $s->selling_price, 'cost' => $s->cost_price]))
                                ->merge($trip->hotelBookings->map(fn($s) => ['type' => 'Hotels', 'name' => $s->hotel_name, 'selling' => $s->selling_price, 'cost' => $s->cost_price]))
                                ->merge($trip->transferBookings->map(fn($s) => ['type' => 'Transfers', 'name' => $s->pickup_location.' → '.$s->dropoff_location, 'selling' => $s->selling_price, 'cost' => $s->cost_price]))
                                ->merge($trip->visaApplications->map(fn($s) => ['type' => 'Visas', 'name' => $s->country, 'selling' => $s->selling_price, 'cost' => $s->cost_price]))
                                ->merge($trip->insurancePolicies->map(fn($s) => ['type' => 'Insurance', 'name' => $s->policy_number ?? 'Policy', 'selling' => $s->selling_price, 'cost' => $s->cost_price]))
                                ->merge($trip->activities->map(fn($s) => ['type' => 'Activities', 'name' => $s->name, 'selling' => $s->selling_price, 'cost' => $s->cost_price]))
                                ->merge($trip->cruiseBookings->map(fn($s) => ['type' => 'Cruises', 'name' => $s->cruise_line ?: 'Cruise', 'selling' => $s->selling_price, 'cost' => $s->cost_price]))
                                ->merge($trip->trainBookings->map(fn($s) => ['type' => 'Trains', 'name' => ($s->train_company ?: '').' '.($s->train_number ?: ''), 'selling' => $s->selling_price, 'cost' => $s->cost_price]))
                                ->merge($trip->carRentals->map(fn($s) => ['type' => 'Car Rentals', 'name' => $s->company ?: 'Car', 'selling' => $s->selling_price, 'cost' => $s->cost_price]))
                                ->merge($trip->packageBookings->map(fn($s) => ['type' => 'Packages', 'name' => $s->package_name ?: 'Package', 'selling' => $s->selling_price, 'cost' => $s->cost_price]))
                                ->merge($trip->otherServices->map(fn($s) => ['type' => 'Other', 'name' => $s->service_name ?: 'Service', 'selling' => $s->selling_price, 'cost' => $s->cost_price]));
                        @endphp
                        @if ($items->isNotEmpty())
                        <table class="w-full text-sm">
                            <thead><tr class="text-left text-gray-500 border-b"><th class="pb-2">Service</th><th class="pb-2">Item</th><th class="pb-2 text-right">Selling</th><th class="pb-2 text-right">Cost</th><th class="pb-2 text-right">Profit</th></tr></thead>
                            <tbody>
                                @foreach ($items as $it)
                                <tr class="border-b border-gray-50"><td class="py-2 text-gray-600">{{ $it['type'] }}</td><td class="py-2 text-gray-900">{{ $it['name'] }}</td><td class="py-2 text-right font-mono">{{ number_format($it['selling'], 2) }}</td><td class="py-2 text-right font-mono">{{ number_format($it['cost'], 2) }}</td><td class="py-2 text-right font-mono {{ ($it['selling'] - $it['cost']) >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($it['selling'] - $it['cost'], 2) }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-sm text-gray-400 text-center py-4">No services yet.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-5 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-wallet text-blue-500 mr-2"></i> Automation Logs</h3></div>
                    <div class="p-4 space-y-2 text-xs">
                        @forelse ($this->automationLogs as $log)
                        <div class="flex items-center gap-2 {{ $log->status === 'success' ? 'text-green-700' : ($log->status === 'failed' ? 'text-red-700' : 'text-yellow-700') }}">
                            <i class="fas {{ $log->status === 'success' ? 'fa-check-circle' : ($log->status === 'failed' ? 'fa-exclamation-circle' : 'fa-clock') }}"></i>
                            <div><p class="font-medium">{{ $log->action }}</p><p class="text-gray-500">{{ $log->created_at->diffForHumans() }}</p></div>
                        </div>
                        @empty
                        <p class="text-gray-400 text-center py-4">No automation logs.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- TAB: PAYMENTS --}}
        @if($activeTab === 'payments')
        <div class="grid grid-cols-1 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-hand-holding-usd text-green-500 mr-2"></i> Payments</h3></div>
                <div class="p-4">
                    @php $payments = $trip->payments()->latest()->get(); @endphp
                    @if ($payments->isNotEmpty())
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-xs text-gray-500 uppercase tracking-wider"><th class="pb-2 pr-4">Date</th><th class="pb-2 pr-4">Amount</th><th class="pb-2 pr-4">Method</th><th class="pb-2 pr-4">Reference</th><th class="pb-2">Status</th></tr></thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($payments as $pmt)
                            <tr class="text-gray-700"><td class="py-2 pr-4 whitespace-nowrap">{{ $pmt->payment_date?->format('d M Y') }}</td><td class="py-2 pr-4 font-mono font-medium">{{ number_format($pmt->amount, 2) }} {{ $pmt->currency }}</td><td class="py-2 pr-4 capitalize">{{ str_replace('_', ' ', $pmt->payment_method) }}</td><td class="py-2 pr-4 text-gray-400">{{ $pmt->reference_number ?: '—' }}</td><td class="py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $pmt->status === 'completed' ? 'bg-green-100 text-green-700' : ($pmt->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">{{ ucfirst($pmt->status) }}</span></td></tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <p class="text-sm text-gray-400 text-center py-6">No payments recorded yet.</p>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- TAB: DOCUMENTS --}}
        @if($activeTab === 'docs')
        <div class="grid grid-cols-1 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-file-alt text-amber-500 mr-2"></i> Documents</h3></div>
                <div class="p-4">@livewire('trips.trip-files', ['trip' => $trip], key('files-'.$trip->id))</div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-envelope text-blue-500 mr-2"></i> Emails</h3></div>
                    <div class="p-4">@livewire('trips.trip-emails', ['trip' => $trip], key('emails-'.$trip->id))</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between"><h3 class="text-sm font-semibold text-gray-900"><i class="fab fa-whatsapp text-green-500 mr-2"></i> WhatsApp</h3></div>
                    <div class="p-4">@livewire('trips.trip-whatsapp', ['trip' => $trip], key('whatsapp-'.$trip->id))</div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-gift text-amber-500 mr-2"></i> Extras</h3></div>
                <div class="p-4">@livewire('trips.trip-benefits', ['trip' => $trip], key('benefits-'.$trip->id))</div>
            </div>
        </div>
        @endif

        {{-- TAB: TASKS --}}
        @if($activeTab === 'tasks')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-t-4 border-t-cyan-400">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-tasks text-cyan-500 mr-2"></i> Tasks</h3></div>
                <div class="p-4">@livewire('trips.trip-tasks', ['trip' => $trip], key('tasks-full-'.$trip->id))</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-t-4 border-t-orange-400">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-receipt text-orange-500 mr-2"></i> Expenses ({{ $trip->expenses->count() }})</h3></div>
                <div class="p-4">@livewire('trips.trip-expenses', ['trip' => $trip], key('expenses-full-'.$trip->id))</div>
            </div>
        </div>
        @endif

        {{-- TAB: NOTES --}}
        @if($activeTab === 'notes')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-t-4 border-t-gray-400">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-sticky-note text-gray-500 mr-2"></i> Notes</h3></div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Trip Notes</label>
                        <textarea wire:model="trip.notes" rows="6" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" readonly></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Internal Notes</label>
                        <textarea rows="6" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-500" readonly>{{ $trip->internal_notes ?? 'No internal notes.' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- TAB: ITINERARY --}}
        @if($activeTab === 'itinerary')
        @php $days = $this->itineraryDays; @endphp
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Day-by-Day Itinerary</h3>
                <div class="flex gap-2">
                    <button wire:click="autoAssignDays" class="px-3 py-1.5 text-xs font-medium bg-rose-50 text-rose-700 rounded-lg hover:bg-rose-100"><i class="fas fa-magic mr-1"></i> Auto-Assign Days</button>
                </div>
            </div>

            @if(count($days) > 0)
            @foreach($days as $day)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 bg-gradient-to-r from-rose-50 to-pink-50 border-b border-gray-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center text-rose-600 text-sm font-bold">{{ $day['day'] }}</div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Day {{ $day['day'] }}</p>
                            <p class="text-xs text-gray-500">{{ $day['label'] }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500">{{ $day['services']->count() }} services</span>
                </div>
                <div class="p-4 space-y-2" x-data="{ dragId: null, dropDay: null }">
                    @forelse($day['services'] as $svc)
                    <div draggable="true"
                         @@dragstart="dragId = '{{ $svc->id }}'"
                         @@dragend="dragId = null"
                         class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors border-l-4 border-l-{{ $svc->display_color }}-400 cursor-grab active:cursor-grabbing">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-8 h-8 rounded-lg bg-{{ $svc->display_color }}-50 flex items-center justify-center shrink-0"><i class="fas {{ $svc->display_icon }} text-{{ $svc->display_color }}-500"></i></div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $svc->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ ucfirst($svc->type) }}
                                    @if($svc->supplier_status)<span class="ml-2 px-1.5 py-0.5 rounded text-xs {{ $svc->supplier_status === 'confirmed' ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' }}">{{ ucfirst($svc->supplier_status) }}</span>@endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <span class="text-sm font-mono font-semibold text-gray-900">{{ number_format($svc->selling_price, 2) }}</span>
                            <button wire:click="unassignServiceDay('{{ $svc->id }}')" class="p-1 text-gray-400 hover:text-red-500" title="Remove from day"><i class="fas fa-times text-xs"></i></button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <div class="w-12 h-12 mx-auto mb-2 rounded-lg bg-gray-50 flex items-center justify-center"><i class="fas fa-plus text-gray-300"></i></div>
                        <p class="text-sm text-gray-400">No services assigned to this day</p>
                        <p class="text-xs text-gray-300 mt-1">Drag services here from other days</p>
                    </div>
                    @endforelse

                    {{-- Drop zone --}}
                    <div @@dragover.prevent @@drop.prevent="
                        if (dragId) {
                            $wire.updateServiceDay(dragId, {{ $day['day'] }});
                            dragId = null;
                        }
                    " class="border-2 border-dashed border-gray-200 rounded-lg p-2 text-center text-xs text-gray-400 hover:border-rose-300 hover:text-rose-500 transition-colors @@dragenter.dragOverlayenter @@dragleave.dragOverlayleave">Drop service here</div>
                </div>
            </div>
            @endforeach
            @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-50 flex items-center justify-center"><i class="fas fa-calendar-day text-gray-300 text-2xl"></i></div>
                <h4 class="text-base font-semibold text-gray-900 mb-1">No Itinerary</h4>
                <p class="text-sm text-gray-500 mb-4">Set a trip start date then click "Auto-Assign Days" to build your itinerary.</p>
                <button wire:click="autoAssignDays" class="px-4 py-2 text-sm font-medium bg-rose-500 text-white rounded-lg hover:bg-rose-600"><i class="fas fa-magic mr-1"></i> Auto-Assign Days</button>
            </div>
            @endif
        </div>
        @endif

        {{-- TAB: TIMELINE --}}
        @if($activeTab === 'timeline')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 border-t-4 border-t-purple-400">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between"><h3 class="text-sm font-semibold text-gray-900"><i class="fas fa-history text-purple-500 mr-2"></i> Timeline</h3></div>
            <div class="p-4">
                @if($trip->timeline->isNotEmpty())
                @php $grouped = $trip->timeline->groupBy(fn($e) => $e->created_at->format('Y-m-d')); @endphp
                <div class="relative pl-8 space-y-6">
                    <div class="absolute left-4 top-2 bottom-2 w-0.5 bg-gray-200"></div>
                    @foreach($grouped as $date => $entries)
                    <div class="relative">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="absolute -left-6 w-3 h-3 rounded-full bg-gray-300 border-2 border-white"></div>
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
                        </div>
                        <div class="space-y-3 ml-2">
                            @foreach($entries as $entry)
                            <div class="relative pl-4 border-l-2
                                @if($entry->type === 'trip_created') border-l-blue-500
                                @elseif(in_array($entry->type, ['payment_received','invoice_created'])) border-l-green-500
                                @elseif(str_contains($entry->type, 'passenger')) border-l-amber-500
                                @elseif(str_contains($entry->type, 'service_added') || str_contains($entry->type, 'service_edited')) border-l-indigo-500
                                @elseif(str_contains($entry->type, 'deleted') || str_contains($entry->type, 'removed')) border-l-red-400
                                @else border-l-gray-400 @endif">
                                <p class="text-sm text-gray-900">{{ $entry->description }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-400 font-mono">{{ $entry->created_at->format('H:i') }}</span>
                                    <span class="text-xs text-gray-400">{{ $entry->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-400 text-center py-6">No timeline entries.</p>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- MODAL: Add/Edit Service --}}
    <div x-show="activeModal" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" x-on:keydown.escape.window="activeModal = null">
        <div @@click.outside="activeModal = null" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[85vh] overflow-y-auto">
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <div class="flex items-center gap-3">
                    <template x-if="activeModal === 'passenger'">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center"><i class="fas fa-users text-blue-500 text-sm"></i></div>
                    </template>
                    <template x-if="activeModal === 'flight'">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center"><i class="fas fa-plane text-indigo-500 text-sm"></i></div>
                    </template>
                    <template x-if="activeModal === 'hotel'">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center"><i class="fas fa-hotel text-emerald-500 text-sm"></i></div>
                    </template>
                    <template x-if="activeModal === 'transfer'">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center"><i class="fas fa-shuttle-van text-orange-500 text-sm"></i></div>
                    </template>
                    <template x-if="activeModal === 'visa'">
                        <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center"><i class="fas fa-passport text-purple-500 text-sm"></i></div>
                    </template>
                    <template x-if="activeModal === 'insurance'">
                        <div class="w-8 h-8 rounded-lg bg-pink-50 flex items-center justify-center"><i class="fas fa-shield-alt text-pink-500 text-sm"></i></div>
                    </template>
                    <template x-if="activeModal === 'activity'">
                        <div class="w-8 h-8 rounded-lg bg-cyan-50 flex items-center justify-center"><i class="fas fa-ticket-alt text-cyan-500 text-sm"></i></div>
                    </template>
                    <template x-if="activeModal === 'cruise'">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center"><i class="fas fa-ship text-sky-500 text-sm"></i></div>
                    </template>
                    <template x-if="activeModal === 'train'">
                        <div class="w-8 h-8 rounded-lg bg-stone-50 flex items-center justify-center"><i class="fas fa-train text-stone-500 text-sm"></i></div>
                    </template>
                    <template x-if="activeModal === 'car'">
                        <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center"><i class="fas fa-car text-yellow-600 text-sm"></i></div>
                    </template>
                    <template x-if="activeModal === 'package'">
                        <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center"><i class="fas fa-box text-teal-500 text-sm"></i></div>
                    </template>
                    <template x-if="activeModal === 'other'">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center"><i class="fas fa-ellipsis-h text-gray-500 text-sm"></i></div>
                    </template>
                    <h3 class="text-base font-semibold text-gray-900" x-text="activeModal === 'car' ? 'Add Car Rental' : activeModal === 'other' ? 'Add Other Service' : 'Add ' + (activeModal ? activeModal.charAt(0).toUpperCase() + activeModal.slice(1) : '')"></h3>
                </div>
                <button @@click="activeModal = null" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-4">
                {{-- PASSENGER FORM --}}
                <template x-if="activeModal === 'passenger'">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">First Name *</label><input type="text" wire:model="p_first_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" placeholder="John" />@error('p_first_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Last Name *</label><input type="text" wire:model="p_last_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500" placeholder="Doe" />@error('p_last_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Date of Birth</label><input type="date" wire:model="p_date_of_birth" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('p_date_of_birth')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Nationality</label><input type="text" wire:model="p_nationality" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Egyptian" />@error('p_nationality')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Passport No.</label><input type="text" wire:model="p_passport_number" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="AB123456" />@error('p_passport_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Passport Expiry</label><input type="date" wire:model="p_passport_expiry" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('p_passport_expiry')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                    </div>
                </template>

                {{-- FLIGHT FORM --}}
                <template x-if="activeModal === 'flight'">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Airline</label><input type="text" wire:model.blur="f_airline" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Turkish Airlines" />@error('f_airline')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Flight #</label><input type="text" wire:model.blur="f_flight_number" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="TK1234" />@error('f_flight_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Departure Airport</label><input type="text" wire:model.blur="f_departure_airport" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="TLV" />@error('f_departure_airport')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Arrival Airport</label><input type="text" wire:model.blur="f_arrival_airport" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="SSH" />@error('f_arrival_airport')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Departure</label><input type="datetime-local" wire:model.blur="f_departure_datetime" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('f_departure_datetime')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Arrival</label><input type="datetime-local" wire:model.blur="f_arrival_datetime" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('f_arrival_datetime')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model.blur="f_selling_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('f_selling_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model.blur="f_cost_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('f_cost_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Currency</label><select wire:model="f_currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select wire:model="f_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>confirmed</option><option>pending</option><option>cancelled</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Supplier</label><select wire:model="f_supplier_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option value="">None</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
                        </div>
                    </div>
                </template>

                {{-- HOTEL FORM --}}
                <template x-if="activeModal === 'hotel'">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Hotel Name</label><input type="text" wire:model="h_hotel_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Hilton" />@error('h_hotel_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">City</label><input type="text" wire:model="h_city" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('h_city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Room Type</label><input type="text" wire:model="h_room_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Deluxe" />@error('h_room_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Rooms</label><input type="number" min="1" wire:model="h_number_of_rooms" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('h_number_of_rooms')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Check In</label><input type="date" wire:model="h_check_in" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('h_check_in')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Check Out</label><input type="date" wire:model="h_check_out" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('h_check_out')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="h_selling_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('h_selling_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="h_cost_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('h_cost_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Currency</label><select wire:model="h_currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select wire:model="h_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>confirmed</option><option>pending</option><option>cancelled</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Supplier</label><select wire:model="h_supplier_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option value="">None</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
                        </div>
                    </div>
                </template>

                {{-- TRANSFER FORM --}}
                <template x-if="activeModal === 'transfer'">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Pickup Location</label><input type="text" wire:model="t_pickup" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('t_pickup')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Dropoff Location</label><input type="text" wire:model="t_dropoff" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('t_dropoff')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Vehicle Type</label><input type="text" wire:model="t_vehicle_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Sedan/Bus" />@error('t_vehicle_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Passengers</label><input type="number" min="1" wire:model="t_passengers" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('t_passengers')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Pickup Date/Time</label><input type="datetime-local" wire:model="t_pickup_datetime" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('t_pickup_datetime')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="t_selling_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('t_selling_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="t_cost_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('t_cost_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Currency</label><select wire:model="t_currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Supplier</label><select wire:model="t_supplier_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option value="">None</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
                        </div>
                    </div>
                </template>

                {{-- VISA FORM --}}
                <template x-if="activeModal === 'visa'">
                    <div class="space-y-4">
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Country</label><input type="text" wire:model="v_country" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Schengen" />@error('v_country')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Visa Type</label><select wire:model="v_visa_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>tourist</option><option>business</option><option>transit</option><option>e-visa</option><option>on_arrival</option></select></div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="v_selling_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('v_selling_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="v_cost_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('v_cost_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Currency</label><select wire:model="v_currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                    </div>
                </template>

                {{-- INSURANCE FORM --}}
                <template x-if="activeModal === 'insurance'">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Type</label><select wire:model="i_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>travel</option><option>medical</option><option>cancellation</option><option>baggage</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Policy #</label><input type="text" wire:model="i_policy_number" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('i_policy_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input type="date" wire:model="i_start_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('i_start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><input type="date" wire:model="i_end_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('i_end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="i_selling_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('i_selling_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="i_cost_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('i_cost_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Currency</label><select wire:model="i_currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                    </div>
                </template>

                {{-- ACTIVITY FORM --}}
                <template x-if="activeModal === 'activity'">
                    <div class="space-y-4">
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Activity Name</label><input type="text" wire:model="a_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Snorkeling" />@error('a_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Location</label><input type="text" wire:model="a_location" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('a_location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Date</label><input type="date" wire:model="a_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('a_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Time</label><input type="time" wire:model="a_time" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('a_time')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="a_selling_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('a_selling_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="a_cost_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('a_cost_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Currency</label><select wire:model="a_currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                    </div>
                </template>

                {{-- CRUISE FORM --}}
                <template x-if="activeModal === 'cruise'">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Cruise Line</label><input type="text" wire:model="cr_cruise_line" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Royal Caribbean" />@error('cr_cruise_line')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Ship Name</label><input type="text" wire:model="cr_ship_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Symphony of the Seas" />@error('cr_ship_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Cabin Type</label><input type="text" wire:model="cr_cabin_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Balcony" />@error('cr_cabin_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Itinerary</label><input type="text" wire:model="cr_itinerary" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Barcelona → Rome" />@error('cr_itinerary')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Departure Port</label><input type="text" wire:model="cr_departure_port" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('cr_departure_port')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Arrival Port</label><input type="text" wire:model="cr_arrival_port" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('cr_arrival_port')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Departure Date</label><input type="date" wire:model="cr_departure_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('cr_departure_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Arrival Date</label><input type="date" wire:model="cr_arrival_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('cr_arrival_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="cr_selling_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('cr_selling_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="cr_cost_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('cr_cost_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Currency</label><select wire:model="cr_currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select wire:model="cr_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>confirmed</option><option>pending</option><option>cancelled</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Supplier</label><select wire:model="cr_supplier_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option value="">None</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
                        </div>
                    </div>
                </template>

                {{-- TRAIN FORM --}}
                <template x-if="activeModal === 'train'">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Train Company</label><input type="text" wire:model="tr_company" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Eurostar" />@error('tr_company')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Train #</label><input type="text" wire:model="tr_train_number" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="ES123" />@error('tr_train_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Departure Station</label><input type="text" wire:model="tr_departure_station" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Paris Gare du Nord" />@error('tr_departure_station')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Arrival Station</label><input type="text" wire:model="tr_arrival_station" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="London St Pancras" />@error('tr_arrival_station')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Departure</label><input type="datetime-local" wire:model="tr_departure_datetime" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('tr_departure_datetime')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Arrival</label><input type="datetime-local" wire:model="tr_arrival_datetime" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('tr_arrival_datetime')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Class</label><input type="text" wire:model="tr_class" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="First Class" />@error('tr_class')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="tr_selling_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('tr_selling_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="tr_cost_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('tr_cost_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Currency</label><select wire:model="tr_currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select wire:model="tr_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>confirmed</option><option>pending</option><option>cancelled</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Supplier</label><select wire:model="tr_supplier_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option value="">None</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
                        </div>
                    </div>
                </template>

                {{-- CAR RENTAL FORM --}}
                <template x-if="activeModal === 'car'">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Company</label><input type="text" wire:model="ca_company" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Hertz" />@error('ca_company')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Car Type</label><input type="text" wire:model="ca_car_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="SUV" />@error('ca_car_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Pickup Location</label><input type="text" wire:model="ca_pickup_location" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('ca_pickup_location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Dropoff Location</label><input type="text" wire:model="ca_dropoff_location" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('ca_dropoff_location')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Pickup</label><input type="datetime-local" wire:model="ca_pickup_datetime" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('ca_pickup_datetime')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Dropoff</label><input type="datetime-local" wire:model="ca_dropoff_datetime" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('ca_dropoff_datetime')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="ca_selling_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('ca_selling_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="ca_cost_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('ca_cost_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Currency</label><select wire:model="ca_currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select wire:model="ca_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>confirmed</option><option>pending</option><option>cancelled</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Supplier</label><select wire:model="ca_supplier_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option value="">None</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
                        </div>
                    </div>
                </template>

                {{-- PACKAGE FORM --}}
                <template x-if="activeModal === 'package'">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Package Name</label><input type="text" wire:model="pk_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="All Inclusive Dubai" />@error('pk_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Type</label><input type="text" wire:model="pk_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="all_inclusive" />@error('pk_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea wire:model="pk_description" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" rows="2"></textarea>@error('pk_description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Start Date</label><input type="date" wire:model="pk_start_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('pk_start_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">End Date</label><input type="date" wire:model="pk_end_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('pk_end_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="pk_selling_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('pk_selling_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="pk_cost_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('pk_cost_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Currency</label><select wire:model="pk_currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select wire:model="pk_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>confirmed</option><option>pending</option><option>cancelled</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Supplier</label><select wire:model="pk_supplier_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option value="">None</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
                        </div>
                    </div>
                </template>

                {{-- OTHER SERVICE FORM --}}
                <template x-if="activeModal === 'other'">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Service Name</label><input type="text" wire:model="o_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Service name" />@error('o_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Type</label><input type="text" wire:model="o_type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="type" />@error('o_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Description</label><textarea wire:model="o_description" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" rows="2"></textarea>@error('o_description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        <div><label class="block text-xs font-medium text-gray-600 mb-1">Date</label><input type="date" wire:model="o_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('o_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        <div class="grid grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Selling Price</label><input type="number" step="0.01" wire:model="o_selling_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('o_selling_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Cost Price</label><input type="number" step="0.01" wire:model="o_cost_price" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" />@error('o_cost_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror</div>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Currency</label><select wire:model="o_currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>USD</option><option>ILS</option><option>JOD</option><option>EUR</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Status</label><select wire:model="o_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option>confirmed</option><option>pending</option><option>cancelled</option></select></div>
                            <div><label class="block text-xs font-medium text-gray-600 mb-1">Supplier</label><select wire:model="o_supplier_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"><option value="">None</option>@foreach ($suppliers as $s)<option value="{{ $s->id }}">{{ $s->company_name }}</option>@endforeach</select></div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3 sticky bottom-0 bg-white">
                <button type="button" @@click="activeModal = null" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="button"
                    x-show="activeModal === 'passenger'" wire:click="savePassenger" wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="savePassenger"><i class="fas fa-save mr-1.5"></i>Save Passenger</span>
                    <span wire:loading wire:target="savePassenger"><i class="fas fa-spinner fa-spin mr-1.5"></i>Saving...</span>
                </button>
                <button type="button"
                    x-show="activeModal === 'flight'" wire:click="saveFlight" wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="saveFlight"><i class="fas fa-save mr-1.5"></i>Save Flight</span>
                    <span wire:loading wire:target="saveFlight"><i class="fas fa-spinner fa-spin mr-1.5"></i>Saving...</span>
                </button>
                <button type="button"
                    x-show="activeModal === 'hotel'" wire:click="saveHotel" wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="saveHotel"><i class="fas fa-save mr-1.5"></i>Save Hotel</span>
                    <span wire:loading wire:target="saveHotel"><i class="fas fa-spinner fa-spin mr-1.5"></i>Saving...</span>
                </button>
                <button type="button"
                    x-show="activeModal === 'transfer'" wire:click="saveTransfer" wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-medium text-white bg-orange-600 rounded-lg hover:bg-orange-700 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="saveTransfer"><i class="fas fa-save mr-1.5"></i>Save Transfer</span>
                    <span wire:loading wire:target="saveTransfer"><i class="fas fa-spinner fa-spin mr-1.5"></i>Saving...</span>
                </button>
                <button type="button"
                    x-show="activeModal === 'visa'" wire:click="saveVisa" wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="saveVisa"><i class="fas fa-save mr-1.5"></i>Save Visa</span>
                    <span wire:loading wire:target="saveVisa"><i class="fas fa-spinner fa-spin mr-1.5"></i>Saving...</span>
                </button>
                <button type="button"
                    x-show="activeModal === 'insurance'" wire:click="saveInsurance" wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-medium text-white bg-pink-600 rounded-lg hover:bg-pink-700 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="saveInsurance"><i class="fas fa-save mr-1.5"></i>Save Insurance</span>
                    <span wire:loading wire:target="saveInsurance"><i class="fas fa-spinner fa-spin mr-1.5"></i>Saving...</span>
                </button>
                <button type="button"
                    x-show="activeModal === 'activity'" wire:click="saveActivity" wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-medium text-white bg-cyan-600 rounded-lg hover:bg-cyan-700 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="saveActivity"><i class="fas fa-save mr-1.5"></i>Save Activity</span>
                    <span wire:loading wire:target="saveActivity"><i class="fas fa-spinner fa-spin mr-1.5"></i>Saving...</span>
                </button>
                <button type="button"
                    x-show="activeModal === 'cruise'" wire:click="saveCruise" wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="saveCruise"><i class="fas fa-save mr-1.5"></i>Save Cruise</span>
                    <span wire:loading wire:target="saveCruise"><i class="fas fa-spinner fa-spin mr-1.5"></i>Saving...</span>
                </button>
                <button type="button"
                    x-show="activeModal === 'train'" wire:click="saveTrain" wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-medium text-white bg-stone-600 rounded-lg hover:bg-stone-700 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="saveTrain"><i class="fas fa-save mr-1.5"></i>Save Train</span>
                    <span wire:loading wire:target="saveTrain"><i class="fas fa-spinner fa-spin mr-1.5"></i>Saving...</span>
                </button>
                <button type="button"
                    x-show="activeModal === 'car'" wire:click="saveCar" wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg hover:bg-yellow-700 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="saveCar"><i class="fas fa-save mr-1.5"></i>Save Car Rental</span>
                    <span wire:loading wire:target="saveCar"><i class="fas fa-spinner fa-spin mr-1.5"></i>Saving...</span>
                </button>
                <button type="button"
                    x-show="activeModal === 'package'" wire:click="savePackage" wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="savePackage"><i class="fas fa-save mr-1.5"></i>Save Package</span>
                    <span wire:loading wire:target="savePackage"><i class="fas fa-spinner fa-spin mr-1.5"></i>Saving...</span>
                </button>
                <button type="button"
                    x-show="activeModal === 'other'" wire:click="saveOther" wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-medium text-white bg-gray-600 rounded-lg hover:bg-gray-700 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="saveOther"><i class="fas fa-save mr-1.5"></i>Save Service</span>
                    <span wire:loading wire:target="saveOther"><i class="fas fa-spinner fa-spin mr-1.5"></i>Saving...</span>
                </button>
            </div>
        </div>
    </div>
</div>