<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Profit Report</h2>
            <p class="text-sm text-gray-500 mt-1">Trip profitability breakdown</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Total Selling</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($summary['total_selling'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Total Cost</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($summary['total_cost'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Total Profit</p>
            <p class="text-xl font-bold {{ $summary['total_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }} mt-1">{{ number_format($summary['total_profit'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Avg Margin</p>
            <p class="text-xl font-bold text-blue-600 mt-1">{{ number_format($summary['avg_margin'], 1) }}%</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Profitable / Losses</p>
            <p class="text-xl font-bold mt-1"><span class="text-green-600">{{ $summary['profitable'] }}</span> / <span class="text-red-600">{{ $summary['losses'] }}</span></p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Trip Profitability</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="px-4 py-3 font-medium">Trip</th>
                        <th class="px-4 py-3 font-medium">Destination</th>
                        <th class="px-4 py-3 font-medium">Date</th>
                        <th class="px-4 py-3 font-medium text-right">Selling</th>
                        <th class="px-4 py-3 font-medium text-right">Cost</th>
                        <th class="px-4 py-3 font-medium text-right">Profit</th>
                        <th class="px-4 py-3 font-medium text-right">Margin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($trips as $t)
                    <tr class="border-b border-gray-100">
                        <td class="px-4 py-3 text-gray-900 font-medium">{{ $t['name'] }}<div class="text-xs text-gray-400">{{ $t['trip_number'] }}</div></td>
                        <td class="px-4 py-3 text-gray-600">{{ $t['destination'] ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $t['start_date'] ?: '—' }}</td>
                        <td class="px-4 py-3 text-right font-mono">{{ number_format($t['selling_price'], 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono">{{ number_format($t['cost_price'], 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-medium {{ $t['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($t['profit'], 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $t['margin'] >= 20 ? 'bg-green-100 text-green-700' : ($t['margin'] >= 10 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ number_format($t['margin'], 1) }}%
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">No trip data available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
