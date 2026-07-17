<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Commission Report</h2>
        <p class="text-sm text-gray-500 mt-1">Commissions earned per trip service</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Date From</label>
                <input type="date" wire:model.live="dateFrom" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Date To</label>
                <input type="date" wire:model.live="dateTo" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Supplier</label>
                <select wire:model.live="supplierId" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">All Suppliers</option>
                    @foreach ($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->company_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button wire:click="$set('dateFrom', '{{ now()->startOfMonth()->format('Y-m-d') }}'); $set('dateTo', '{{ now()->endOfMonth()->format('Y-m-d') }}')" class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">This Month</button>
                <button wire:click="$set('dateFrom', '{{ now()->startOfYear()->format('Y-m-d') }}'); $set('dateTo', '{{ now()->endOfYear()->format('Y-m-d') }}')" class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">This Year</button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Total Selling</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($totalSelling, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Total Cost</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($totalCost, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Total Commission</p>
            <p class="text-xl font-bold text-green-600 mt-1">{{ number_format($totalCommission, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Commission Breakdown</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="px-4 py-3 font-medium cursor-pointer" wire:click="sortBy('supplier_name')">Supplier</th>
                        <th class="px-4 py-3 font-medium cursor-pointer" wire:click="sortBy('service_type')">Service Type</th>
                        <th class="px-4 py-3 font-medium text-right cursor-pointer" wire:click="sortBy('selling_price')">Selling Price</th>
                        <th class="px-4 py-3 font-medium text-right cursor-pointer" wire:click="sortBy('cost_price')">Cost Price</th>
                        <th class="px-4 py-3 font-medium text-right cursor-pointer" wire:click="sortBy('commission')">Commission</th>
                        <th class="px-4 py-3 font-medium text-right">Margin %</th>
                        <th class="px-4 py-3 font-medium cursor-pointer" wire:click="sortBy('trip_name')">Trip</th>
                        <th class="px-4 py-3 font-medium cursor-pointer" wire:click="sortBy('date')">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($services as $s)
                    <tr class="border-b border-gray-100">
                        <td class="px-4 py-3">
                            @if ($s['supplier_id'])
                            <a href="{{ route('suppliers.show', $s['supplier_id']) }}" class="text-blue-600 hover:underline font-medium">{{ $s['supplier_name'] }}</a>
                            @else
                            <span class="text-gray-500">{{ $s['supplier_name'] }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full
                                @switch($s['service_type'])
                                    @case('Flight') bg-sky-100 text-sky-700 @break
                                    @case('Hotel') bg-purple-100 text-purple-700 @break
                                    @case('Transfer') bg-amber-100 text-amber-700 @break
                                    @case('Activity') bg-emerald-100 text-emerald-700 @break
                                    @case('Visa') bg-rose-100 text-rose-700 @break
                                    @case('Insurance') bg-indigo-100 text-indigo-700 @break
                                    @case('Cruise') bg-cyan-100 text-cyan-700 @break
                                    @case('Train') bg-stone-100 text-stone-700 @break
                                    @case('Car Rental') bg-yellow-100 text-yellow-700 @break
                                    @case('Package') bg-teal-100 text-teal-700 @break
                                    @case('Other') bg-gray-100 text-gray-700 @break
                                    @default bg-gray-100 text-gray-700
                                @endswitch
                            ">{{ $s['service_type'] }}</span>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $s['service_detail'] }}</div>
                        </td>
                        <td class="px-4 py-3 text-right font-mono">{{ number_format($s['selling_price'], 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono">{{ number_format($s['cost_price'], 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-medium {{ $s['commission'] >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($s['commission'], 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            @php $margin = $s['selling_price'] > 0 ? ($s['commission'] / $s['selling_price']) * 100 : 0; @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $margin >= 20 ? 'bg-green-100 text-green-700' : ($margin >= 10 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ number_format($margin, 1) }}%
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($s['trip_id'])
                            <a href="{{ route('trips.show', $s['trip_id']) }}" class="text-blue-600 hover:underline">{{ $s['trip_name'] }}</a>
                            @else
                            <span class="text-gray-500">{{ $s['trip_name'] }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $s['date'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No services found for the selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
