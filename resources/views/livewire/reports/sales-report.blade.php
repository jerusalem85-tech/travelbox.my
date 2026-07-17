<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Sales Report</h2>
            <p class="text-sm text-gray-500 mt-1">Overview of bookings, revenue, and costs</p>
        </div>
        <select wire:model.live="period" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
            <option value="monthly">Monthly</option>
            <option value="yearly">Yearly</option>
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Total Bookings</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($summary['total_bookings']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Total Revenue</p>
            <p class="text-xl font-bold text-green-600 mt-1">{{ number_format($summary['total_revenue'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Total Cost</p>
            <p class="text-xl font-bold text-red-600 mt-1">{{ number_format($summary['total_cost'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Total Invoiced</p>
            <p class="text-xl font-bold text-blue-600 mt-1">{{ number_format($summary['total_invoiced'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Pending Invoices</p>
            <p class="text-xl font-bold text-amber-600 mt-1">{{ number_format($summary['pending_invoices']) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Revenue vs Cost ({{ $period === 'yearly' ? 'Yearly' : 'Monthly' }})</h3>
        @if (count($chartData) > 0)
        <div class="space-y-3">
            @php $maxVal = max(max(array_column($chartData, 'revenue')), max(array_column($chartData, 'cost')), 1); @endphp
            @foreach ($chartData as $row)
            <div class="space-y-1">
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span class="font-medium">{{ $row['period'] }}</span>
                    <span>{{ number_format($row['revenue'], 0) }} / {{ number_format($row['cost'], 0) }}</span>
                </div>
                <div class="h-5 bg-gray-100 rounded-full overflow-hidden flex">
                    <div class="h-full bg-green-500 rounded-l-full transition-all" style="width: {{ ($row['revenue'] / $maxVal) * 100 }}%"></div>
                    <div class="h-full bg-red-400 rounded-r-full transition-all" style="width: {{ ($row['cost'] / $maxVal) * 100 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-center py-8">No data available for this period.</p>
        @endif
    </div>
</div>
