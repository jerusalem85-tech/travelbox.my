<div class="space-y-6">
    {{-- Operational KPIs --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 border-t-4 border-t-rose-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Today's Departures</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $todayDepartures }}</p>
                </div>
                <div class="bg-rose-100 p-3 rounded-lg"><svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg></div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Trips departing today</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 border-t-4 border-t-amber-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Supplier Payments Due</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($supplierPaymentsDue, 2) }}</p>
                </div>
                <div class="bg-amber-100 p-3 rounded-lg"><svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></div>
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ $pendingExpenses }} pending expenses</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 border-t-4 border-t-violet-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Customer Balance Owing</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($customerBalanceOwing, 2) }}</p>
                </div>
                <div class="bg-violet-100 p-3 rounded-lg"><svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Unpaid invoices</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 border-t-4 border-t-teal-400">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active Trips</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $activeBookings }}</p>
                </div>
                <div class="bg-teal-100 p-3 rounded-lg"><svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></div>
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ $pendingInvoices }} pending invoices</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Trips</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalTrips) }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg"><svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg></div>
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ $activeBookings }} active bookings</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Customers</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalCustomers) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg"><svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg></div>
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ $totalSuppliers }} suppliers</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Monthly Revenue</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($monthlyRevenue, 2) }}</p>
                </div>
                <div class="bg-emerald-100 p-3 rounded-lg"><svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
            </div>
            <p class="text-xs {{ $monthlyProfit >= 0 ? 'text-green-500' : 'text-red-500' }} mt-2">Profit: {{ number_format($monthlyProfit, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Outstanding</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($outstandingBalance, 2) }}</p>
                </div>
                <div class="bg-amber-100 p-3 rounded-lg"><svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ $pendingInvoices }} pending invoices</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Monthly Revenue Trend</h3>
                </div>
                <div class="p-5">
                    <div class="flex items-end gap-3 h-32">
                        @php $max = max(array_column($monthlyData, 'revenue')) ?: 1; @endphp
                        @foreach ($monthlyData as $m)
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <span class="text-xs font-medium text-gray-700">{{ number_format($m['revenue'], 0) }}</span>
                            <div class="w-full bg-blue-100 rounded-t" style="height: {{ ($m['revenue'] / $max) * 100 }}%; min-height: 4px; max-height: 100%;"></div>
                            <span class="text-xs text-gray-500">{{ $m['month'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Upcoming Departures</h3>
                </div>
                <div class="p-5">
                    @if (count($upcomingTrips) > 0)
                    <div class="space-y-3">
                        @foreach ($upcomingTrips as $trip)
                        <a href="{{ route('trips.show', $trip['id']) }}" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 text-xs font-bold">{{ \Carbon\Carbon::parse($trip['start_date'])->format('d') }}</div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $trip['name'] ?: 'Untitled' }}</p>
                                    <p class="text-xs text-gray-500">{{ $trip['destination'] ?: '—' }} &middot; {{ isset($trip['customer']['first_name']) ? $trip['customer']['first_name'].' '.$trip['customer']['last_name'] : '—' }}</p>
                                </div>
                            </div>
                            <x-status-badge :status="$trip['status']">{{ str_replace('_', ' ', ucfirst($trip['status'])) }}</x-status-badge>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-8">No upcoming trips.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                </div>
                <div class="p-5">
                    @if (count($recentActivity) > 0)
                    <div class="space-y-3">
                        @foreach ($recentActivity as $event)
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 mt-1 w-2 h-2 rounded-full {{ match($event['type']) { 'service_added' => 'bg-green-500', 'service_deleted' => 'bg-red-500', 'passenger_added' => 'bg-blue-500', 'passenger_removed' => 'bg-red-500', 'file_added' => 'bg-purple-500', default => 'bg-amber-500' } }}"></div>
                            <div class="min-w-0">
                                <p class="text-sm text-gray-700">{{ $event['description'] }}</p>
                                <p class="text-xs text-gray-400">{{ isset($event['trip']['name']) ? $event['trip']['name'].' — ' : '' }}{{ \Carbon\Carbon::parse($event['created_at'])->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 text-center py-8">No recent activity.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Trip Status</h3>
                </div>
                <div class="p-5 space-y-4">
                    @php
                        $labels = ['enquiry' => ['Enquiry', 'bg-yellow-500'], 'confirmed' => ['Confirmed', 'bg-blue-500'], 'in_progress' => ['In Progress', 'bg-indigo-500'], 'completed' => ['Completed', 'bg-green-500'], 'cancelled' => ['Cancelled', 'bg-red-500']];
                        $total = array_sum($statusCounts) ?: 1;
                    @endphp
                    @foreach (['enquiry', 'confirmed', 'in_progress', 'completed', 'cancelled'] as $s)
                        @php $count = $statusCounts[$s] ?? 0; @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-600">{{ $labels[$s][0] }}</span>
                                <span class="font-medium">{{ $count }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="{{ $labels[$s][1] }} h-2 rounded-full transition-all" style="width: {{ ($count / $total) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Service Mix</h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach ($serviceMix as $label => $count)
                        <div class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg">
                            <span class="text-xs text-gray-600">{{ $label }}</span>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($count) }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-900">Top Customers</h3>
                </div>
                <div class="p-5 space-y-3">
                    @forelse ($topCustomers as $c)
                    <div class="flex items-center justify-between">
                        <a href="{{ route('customers.show', $c['id']) }}" class="text-sm text-blue-600 hover:underline truncate">{{ $c['first_name'] }} {{ $c['last_name'] }}</a>
                        <span class="text-sm font-mono text-gray-700">{{ number_format($c['total_revenue'], 0) }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-400 text-center py-2">No customers yet</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Quick Links</h3>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('trips.create') }}" class="px-3 py-2 text-xs font-medium text-center text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100">New Trip</a>
                    <a href="{{ route('trips.index') }}" class="px-3 py-2 text-xs font-medium text-center text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">All Trips</a>
                    <a href="{{ route('customers.create') }}" class="px-3 py-2 text-xs font-medium text-center text-green-700 bg-green-50 rounded-lg hover:bg-green-100">New Customer</a>
                    <a href="{{ route('invoices.create') }}" class="px-3 py-2 text-xs font-medium text-center text-amber-700 bg-amber-50 rounded-lg hover:bg-amber-100">New Invoice</a>
                    <a href="{{ route('reports.profit') }}" class="px-3 py-2 text-xs font-medium text-center text-indigo-700 bg-indigo-50 rounded-lg hover:bg-indigo-100">Reports</a>
                    <a href="{{ route('trips.pipeline') }}" class="px-3 py-2 text-xs font-medium text-center text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100">Pipeline</a>
                </div>
            </div>
        </div>
    </div>
</div>
