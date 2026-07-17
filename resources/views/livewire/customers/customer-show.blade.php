<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm">
                <span class="text-xl font-bold text-white">{{ substr($customer->first_name, 0, 1) }}{{ substr($customer->last_name, 0, 1) }}</span>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="text-xl font-bold text-gray-900">{{ $customer->full_name }}</h2>
                    @if($customer->loyalty_points > 0)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            {{ $customer->loyalty_points }} pts
                        </span>
                    @endif
                </div>
                <p class="text-sm text-gray-500">{{ $customer->customer_code }} &middot; {{ ucfirst($customer->type) }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                Edit
            </a>
            <a href="{{ route('customers.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Contact Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Contact Information</h3>
                </div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Email</p>
                        <p class="text-gray-900">{{ $customer->email ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Phone</p>
                        <p class="text-gray-900">{{ $customer->phone ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Mobile</p>
                        <p class="text-gray-900">{{ $customer->mobile ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Nationality</p>
                        <p class="text-gray-900">{{ $customer->nationality ?: '—' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-gray-500">Address</p>
                        <p class="text-gray-900">{{ $customer->address ?: '—' }}</p>
                        @if ($customer->city || $customer->country)
                            <p class="text-gray-600">{{ $customer->city }}{{ $customer->city && $customer->country ? ', ' : '' }}{{ $customer->country }}</p>
                        @endif
                    </div>
                </div>
            </div>

            @if ($customer->company_name)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Company</h3></div>
                <div class="p-5 text-sm">
                    <p class="text-gray-900 font-medium">{{ $customer->company_name }}</p>
                </div>
            </div>
            @endif

            {{-- Passport & Personal Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Passport & Personal Info</h3>
                </div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Passport Number</p>
                        <p class="text-gray-900 font-mono font-semibold">{{ $customer->passport_number ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Expiry Date</p>
                        <p class="text-gray-900">{{ $customer->passport_expiry?->format('M d, Y') ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Issue Date</p>
                        <p class="text-gray-900">{{ $customer->passport_issue_date?->format('M d, Y') ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Issue Place</p>
                        <p class="text-gray-900">{{ $customer->passport_issue_place ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Date of Birth</p>
                        <p class="text-gray-900">{{ $customer->date_of_birth?->format('M d, Y') ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Place of Birth</p>
                        <p class="text-gray-900">{{ $customer->place_of_birth ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Sex</p>
                        <p class="text-gray-900">{{ $customer->sex ?: '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Favorite Destinations --}}
            @if($customer->favorite_destinations)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Favorite Destinations</h3></div>
                <div class="p-5">
                    <div class="flex flex-wrap gap-2">
                        @foreach(explode(',', $customer->favorite_destinations) as $dest)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ trim($dest) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Travel History --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">Travel History ({{ $totalTrips }} trips)</h3>
                        <a href="{{ route('trips.index', ['customer' => $customer->id]) }}" class="text-xs text-blue-600 hover:underline">View All</a>
                    </div>
                </div>
                @if($recentTrips->isNotEmpty())
                <div class="divide-y divide-gray-100">
                    @foreach($recentTrips as $trip)
                    <a href="{{ route('trips.show', $trip) }}" class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center text-indigo-500 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $trip->name ?: $trip->destination ?: $trip->trip_number }}</p>
                                <p class="text-xs text-gray-500">{{ $trip->destination ?: '—' }} &middot; {{ $trip->start_date?->format('M d, Y') }} - {{ $trip->end_date?->format('M d, Y') }} &middot; {{ $trip->flightSegments->count() + $trip->hotelBookings->count() }} services</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($trip->total_selling_price, 2) }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                @if($trip->status === 'completed') bg-green-50 text-green-700
                                @elseif($trip->status === 'confirmed') bg-blue-50 text-blue-700
                                @elseif($trip->status === 'cancelled') bg-red-50 text-red-700
                                @else bg-gray-50 text-gray-600 @endif">
                                {{ ucfirst($trip->status) }}
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="p-6 text-center">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <p class="text-sm text-gray-400">No trips yet</p>
                    <a href="{{ route('trips.create', ['customer' => $customer->id]) }}" class="text-xs text-blue-600 hover:underline mt-1 inline-block">Create a trip</a>
                </div>
                @endif
            </div>

            {{-- Visas --}}
            @if($customer->visa_info)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Visa Information ({{ count($customer->visa_info) }})</h3></div>
                <div class="p-5">
                    <div class="space-y-3">
                        @foreach($customer->visa_info as $visa)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $visa['country'] ?? '—' }}</p>
                                <p class="text-xs text-gray-500">{{ $visa['type'] ?? '—' }} &middot; {{ isset($visa['issue']) ? 'Issued: '.$visa['issue'] : '' }} {{ isset($visa['expiry']) ? 'Exp: '.$visa['expiry'] : '' }}</p>
                            </div>
                            @if(isset($visa['status']))
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($visa['status'] === 'active' || $visa['status'] === 'valid') bg-green-50 text-green-700
                                    @else bg-red-50 text-red-700 @endif">
                                    {{ ucfirst($visa['status']) }}
                                </span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Additional Contacts --}}
            @if ($customer->contacts->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Additional Contacts</h3></div>
                <div class="p-5">
                    @foreach ($customer->contacts as $contact)
                        <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $contact->name }}</p>
                                <p class="text-xs text-gray-500">{{ $contact->email }} {{ $contact->phone ? '&middot; ' . $contact->phone : '' }}</p>
                            </div>
                            <span class="text-xs text-gray-400 capitalize">{{ $contact->relationship }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Notes --}}
            @if ($customer->notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Notes</h3></div>
                <div class="p-5 text-sm text-gray-700 whitespace-pre-wrap">{{ $customer->notes }}</div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Summary --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Summary</h3></div>
                <div class="p-5 space-y-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Status</span>
                        <x-status-badge :status="$customer->is_active ? 'active' : 'inactive'">{{ $customer->is_active ? 'Active' : 'Inactive' }}</x-status-badge>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Currency</span>
                        <span class="font-medium text-gray-900">{{ $customer->preferred_currency }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Credit Limit</span>
                        <span class="font-mono text-gray-900">{{ number_format($customer->credit_limit, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Outstanding Balance</span>
                        <span class="font-mono {{ $customer->current_balance > 0 ? 'text-red-600 font-semibold' : 'text-green-600 font-semibold' }}">
                            {{ number_format($customer->current_balance, 2) }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Loyalty Points</span>
                        <span class="font-semibold text-amber-600">{{ number_format($customer->loyalty_points ?? 0) }}</span>
                    </div>
                    <div class="pt-2 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Total Trips</span>
                            <span class="font-medium text-gray-900">{{ $totalTrips }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-gray-500">Total Spent</span>
                            <span class="font-mono font-semibold text-gray-900">{{ number_format($totalSpent, 2) }}</span>
                        </div>
                        @if($destinations->isNotEmpty())
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <p class="text-xs text-gray-400 mb-2">Destinations visited:</p>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($destinations as $dest)
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">{{ $dest }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-xs text-gray-400">Created {{ $customer->created_at->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-400">Updated {{ $customer->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            {{-- Family Members --}}
            @if ($customer->familyMembers->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Family Members</h3></div>
                <div class="p-5 space-y-3">
                    @foreach ($customer->familyMembers as $member)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                                <span class="text-xs font-medium text-gray-600">{{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $member->full_name }}</p>
                                <p class="text-xs text-gray-500 capitalize">{{ $member->relationship }} {{ $member->date_of_birth ? '&middot; ' . $member->date_of_birth->format('M d, Y') : '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Recent Invoices --}}
            @if($customer->invoices->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Recent Invoices</h3></div>
                <div class="divide-y divide-gray-100">
                    @foreach($customer->invoices->take(5) as $inv)
                    <a href="{{ route('invoices.show', $inv) }}" class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition-colors">
                        <div>
                            <p class="text-sm font-medium text-gray-900 font-mono">{{ $inv->invoice_number }}</p>
                            <p class="text-xs text-gray-500">{{ $inv->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-mono font-semibold text-gray-900">{{ number_format($inv->total, 2) }}</p>
                            <span class="text-xs {{ $inv->status === 'paid' ? 'text-green-600' : ($inv->status === 'overdue' ? 'text-red-600' : 'text-amber-600') }}">
                                {{ ucfirst($inv->status) }}
                            </span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
