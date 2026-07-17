<div>
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-start justify-center pt-12" x-data x-on:keydown.escape.window="$wire.closeModal">
        <div class="fixed inset-0 bg-indigo-500/20" wire:click="closeModal"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-y-auto z-10">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10">
                <h2 class="text-lg font-semibold text-gray-900">{{ $editingId ? 'Edit' : 'Add' }} {{ ucfirst($serviceType) }}</h2>
                <button wire:click="closeModal" class="p-1 text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>
            <div class="p-6">
                <form wire:submit="save" class="space-y-4">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 {{ $confirmationPath ? 'bg-green-50 border-green-300' : 'bg-gray-50' }}">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <svg class="w-8 h-8 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            <div class="text-sm">
                                @if ($confirmationPath)
                                    <p class="text-green-700 font-medium">✓ {{ $confirmationName ?? 'File uploaded' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Upload confirmation to attach to this service</p>
                                @else
                                    <p class="text-gray-700 font-medium">Upload confirmation screenshot or PDF</p>
                                    <p class="text-xs text-gray-500 mt-0.5">JPG, PNG or PDF &middot; Max 10 MB</p>
                                @endif
                                <input type="file" wire:model="confirmationUpload" accept="image/*,.pdf" class="hidden" />
                            </div>
                        </label>
                        @if ($confirmationPath)
                        <div class="mt-3 space-y-3">
                            <div class="flex items-center gap-3">
                                @if (str_starts_with(Storage::disk('public')->mimeType($confirmationPath), 'image/'))
                                <img src="{{ Storage::disk('public')->url($confirmationPath) }}" class="max-h-32 rounded border border-gray-200" />
                                @else
                                <div class="flex items-center gap-2 text-sm text-gray-600 bg-white rounded px-3 py-2 border border-gray-200">
                                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3H19v1h1.5V11H19v2h-1.5V7h3v1.5zM9 20v-1.5H3.5V20H9zm8.5 0v-1.5h-5V20h5zM22 20v-1.5h-5V20h5z"/></svg>
                                    <span>PDF document</span>
                                </div>
                                @endif
                                <button type="button" wire:click="removeConfirmation" class="text-xs text-red-600 hover:underline">Remove</button>
                            </div>

                            @if ($serviceType === 'flight' && $ocrStatus === 'idle')
                            <button type="button" wire:click="scanConfirmation" class="w-full py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Scan with OCR – Extract Flight Details
                            </button>
                            @endif

                            @if ($ocrStatus === 'processing')
                            <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <svg class="w-5 h-5 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                                <span class="text-sm text-blue-700 font-medium">Scanning document with OCR...</span>
                            </div>
                            @endif

                            @if ($ocrStatus === 'done' && !empty($ocrResults))
                            <div class="bg-white border border-emerald-200 rounded-lg overflow-hidden">
                                <div class="bg-emerald-50 px-3 py-2 border-b border-emerald-200 flex items-center justify-between">
                                    <span class="text-xs font-semibold text-emerald-700">OCR Results</span>
                                    <button type="button" wire:click="applyOcrResults" class="text-xs font-medium text-white bg-emerald-600 px-3 py-1 rounded hover:bg-emerald-700 transition-colors">
                                        Apply to Form
                                    </button>
                                </div>
                                <div class="p-3 space-y-2 text-xs">
                                    @if ($ocrResults['booking_reference'] ?? null)
                                    <div class="flex items-center gap-2"><span class="text-gray-500">PNR:</span><span class="font-mono font-bold text-gray-800">{{ $ocrResults['booking_reference'] }}</span></div>
                                    @endif
                                    @if (count($ocrResults['passengers'] ?? []) > 0)
                                    <div><span class="text-gray-500">Passengers:</span>
                                        <ul class="list-disc list-inside text-gray-700 mt-0.5">
                                            @foreach ($ocrResults['passengers'] as $p)
                                            <li>{{ $p }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    @if (count($ocrResults['segments'] ?? []) > 0)
                                    <div><span class="text-gray-500">Flights found:</span>
                                        @foreach ($ocrResults['segments'] as $seg)
                                        <div class="mt-1 p-2 bg-gray-50 rounded border border-gray-200">
                                            <span class="font-medium">{{ $seg['airline'] ?? '' }} {{ $seg['flight_number'] ?? '' }}</span>
                                            @if ($seg['departure_airport'] && $seg['arrival_airport'])
                                            <span class="text-gray-500"> {{ $seg['departure_airport'] }} → {{ $seg['arrival_airport'] }}</span>
                                            @endif
                                            @if ($seg['departure_datetime'])
                                            <span class="block text-gray-400 mt-0.5">{{ $seg['departure_datetime'] }}</span>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <details class="text-xs">
                                <summary class="text-gray-400 cursor-pointer hover:text-gray-600">Raw extracted text</summary>
                                <pre class="mt-2 p-2 bg-gray-50 rounded border border-gray-200 text-[10px] text-gray-500 max-h-40 overflow-y-auto whitespace-pre-wrap">{{ $ocrRawText }}</pre>
                            </details>
                            @endif

                            @if ($ocrStatus === 'error')
                            <div class="flex items-center gap-2 p-3 bg-red-50 rounded-lg border border-red-200">
                                <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-xs text-red-700">OCR failed. Try a clearer file.</span>
                                <button type="button" wire:click="scanConfirmation" class="text-xs text-red-600 hover:underline ml-auto">Retry</button>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    @if ($serviceType === 'flight')
                    <div class="mb-4">
                        <label class="block text-xs font-medium text-gray-700 mb-2">Booking Type</label>
                        <div class="flex gap-2">
                            <button type="button" wire:click="$set('flight_booking_type', 'one_way')" class="px-3 py-1.5 text-xs font-medium rounded {{ $flight_booking_type === 'one_way' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">One Way</button>
                            <button type="button" wire:click="$set('flight_booking_type', 'round_trip')" class="px-3 py-1.5 text-xs font-medium rounded {{ $flight_booking_type === 'round_trip' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Round Trip</button>
                            <button type="button" wire:click="$set('flight_booking_type', 'multi_city')" class="px-3 py-1.5 text-xs font-medium rounded {{ $flight_booking_type === 'multi_city' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">Multi Cities</button>
                        </div>
                    </div>

                    @foreach ($flight_segments as $idx => $segment)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-semibold text-gray-800">
                                @if ($flight_booking_type === 'round_trip')
                                    {{ $idx === 0 ? 'Outbound' : 'Return' }}
                                @elseif ($flight_booking_type === 'multi_city')
                                    Segment {{ $idx + 1 }}
                                @else
                                    Flight Details
                                @endif
                            </h4>
                            @if ($flight_booking_type === 'multi_city' && count($flight_segments) > 1)
                                <button type="button" wire:click="removeSegment({{ $idx }})" class="text-red-500 hover:text-red-700 text-xs font-medium">Remove</button>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Class</label>
                                <select wire:model="flight_segments.{{ $idx }}.class" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                    <option value="economy">Economy</option><option value="premium_economy">Premium Economy</option><option value="business">Business</option><option value="first">First</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Flight Number *</label>
                                <input type="text" wire:model="flight_segments.{{ $idx }}.flight_number" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="e.g. TK1234" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Airline *</label>
                                <input type="text" wire:model="flight_segments.{{ $idx }}.airline" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Departure Airport *</label>
                                <input type="text" wire:model="flight_segments.{{ $idx }}.departure_airport" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Arrival Airport *</label>
                                <input type="text" wire:model="flight_segments.{{ $idx }}.arrival_airport" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Departure</label>
                                <input type="datetime-local" wire:model="flight_segments.{{ $idx }}.departure_datetime" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Arrival</label>
                                <input type="datetime-local" wire:model="flight_segments.{{ $idx }}.arrival_datetime" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Booking Reference</label>
                                <input type="text" wire:model="flight_segments.{{ $idx }}.booking_reference" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Ticket Number</label>
                                <input type="text" wire:model="flight_segments.{{ $idx }}.ticket_number" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @if ($flight_booking_type === 'multi_city')
                        <button type="button" wire:click="addSegment" class="mb-4 text-sm font-medium text-blue-600 hover:text-blue-800">+ Add Segment</button>
                    @endif

                    @elseif ($serviceType === 'hotel')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700">Hotel Name *</label>
                            <input type="text" wire:model="hotel_name" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">City</label>
                            <input type="text" wire:model="hotel_city" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Room Type</label>
                            <input type="text" wire:model="room_type" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Check In</label>
                            <input type="date" wire:model="check_in" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Check Out</label>
                            <input type="date" wire:model="check_out" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Meal Plan</label>
                            <select wire:model="meal_plan" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                <option value="">None</option><option value="room_only">Room Only</option><option value="bb">Bed & Breakfast</option><option value="hb">Half Board</option><option value="fb">Full Board</option><option value="ai">All Inclusive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Rooms</label>
                            <input type="number" wire:model="number_of_rooms" min="1" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Booking Reference</label>
                            <input type="text" wire:model="booking_reference" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                    </div>

                    @elseif ($serviceType === 'transfer')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Type</label>
                            <select wire:model="transfer_type" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                <option value="arrival">Arrival</option><option value="departure">Departure</option><option value="inter_hotel">Inter-Hotel</option><option value="excursion">Excursion</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Vehicle Type</label>
                            <input type="text" wire:model="vehicle_type" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Pickup Location *</label>
                            <input type="text" wire:model="pickup_location" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Dropoff Location *</label>
                            <input type="text" wire:model="dropoff_location" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Date/Time</label>
                            <input type="datetime-local" wire:model="pickup_datetime" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Passengers</label>
                            <input type="number" wire:model="number_of_passengers" min="1" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Booking Reference</label>
                            <input type="text" wire:model="booking_reference" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                    </div>

                    @elseif ($serviceType === 'visa')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Country *</label>
                            <input type="text" wire:model="visa_country" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Visa Type</label>
                            <select wire:model="visa_type" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                <option value="tourist">Tourist</option><option value="business">Business</option><option value="transit">Transit</option><option value="student">Student</option><option value="work">Work</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Application Date</label>
                            <input type="date" wire:model="application_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Expected Delivery</label>
                            <input type="date" wire:model="expected_delivery_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                    </div>

                    @elseif ($serviceType === 'insurance')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Policy Number</label>
                            <input type="text" wire:model="policy_number" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Type</label>
                            <select wire:model="insurance_type" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                <option value="travel">Travel</option><option value="medical">Medical</option><option value="cancellation">Cancellation</option><option value="baggage">Baggage</option><option value="comprehensive">Comprehensive</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Start Date</label>
                            <input type="date" wire:model="insurance_start_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">End Date</label>
                            <input type="date" wire:model="insurance_end_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700">Coverage Details</label>
                            <textarea wire:model="coverage_details" rows="2" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"></textarea>
                        </div>
                    </div>

                    @elseif ($serviceType === 'activity')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700">Activity Name *</label>
                            <input type="text" wire:model="activity_name" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Type</label>
                            <input type="text" wire:model="activity_type" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Location</label>
                            <input type="text" wire:model="activity_location" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Date</label>
                            <input type="date" wire:model="activity_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Time</label>
                            <input type="time" wire:model="activity_time" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Duration</label>
                            <input type="text" wire:model="duration" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="e.g. 3 hours" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Participants</label>
                            <input type="number" wire:model="number_of_participants" min="1" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Booking Reference</label>
                            <input type="text" wire:model="booking_reference" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                    </div>
                    @endif

                    <hr class="border-gray-200" />
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Supplier</label>
                            <select wire:model="supplier_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                <option value="">None</option>
                                @foreach ($suppliers as $s)
                                <option value="{{ $s->id }}">{{ $s->company_name }} ({{ str_replace('_', ' ', ucfirst($s->type)) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Status</label>
                            <select wire:model="service_status" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                <option value="requested">Requested</option><option value="confirmed">Confirmed</option><option value="ticketed">Ticketed</option><option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Cost Price</label>
                            <input type="number" step="0.01" min="0" wire:model="cost_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Selling Price</label>
                            <input type="number" step="0.01" min="0" wire:model="selling_price" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700">Currency</label>
                            <select wire:model="currency" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                <option value="USD">USD</option><option value="ILS">ILS</option><option value="JOD">JOD</option><option value="EUR">EUR</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700">Notes</label>
                            <textarea wire:model="service_notes" rows="2" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"></textarea>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">{{ $editingId ? 'Update' : 'Save' }}</button>
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
