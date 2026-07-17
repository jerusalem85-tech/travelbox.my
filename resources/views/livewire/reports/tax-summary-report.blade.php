<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Tax Summary Report</h2>
        <p class="text-sm text-gray-500 mt-1">Monthly tax breakdown from invoices</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Date From</label>
                <input type="date" wire:model.live="dateFrom" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Date To</label>
                <input type="date" wire:model.live="dateTo" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="flex items-end space-x-2">
                <button wire:click="$set('dateFrom', '{{ now()->startOfYear()->format('Y-m-d') }}'); $set('dateTo', '{{ now()->endOfYear()->format('Y-m-d') }}')" class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">This Year</button>
                <button wire:click="$set('dateFrom', '{{ now()->subYear()->startOfYear()->format('Y-m-d') }}'); $set('dateTo', '{{ now()->subYear()->endOfYear()->format('Y-m-d') }}')" class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Last Year</button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Grand Subtotal</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ number_format($grandSubtotal, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Grand Tax</p>
            <p class="text-xl font-bold text-amber-600 mt-1">{{ number_format($grandTax, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Grand Total</p>
            <p class="text-xl font-bold text-blue-600 mt-1">{{ number_format($grandTotal, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Monthly Breakdown</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="px-4 py-3 font-medium">Month</th>
                        <th class="px-4 py-3 font-medium text-right"># Invoices</th>
                        <th class="px-4 py-3 font-medium text-right">Subtotal</th>
                        <th class="px-4 py-3 font-medium text-right">Tax</th>
                        <th class="px-4 py-3 font-medium text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($months as $m)
                    <tr class="border-b border-gray-100">
                        <td class="px-4 py-3 text-gray-900 font-medium">{{ $m['month'] }}</td>
                        <td class="px-4 py-3 text-right">{{ $m['count'] }}</td>
                        <td class="px-4 py-3 text-right font-mono">{{ number_format($m['subtotal'], 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono text-amber-600">{{ number_format($m['tax'], 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-medium">{{ number_format($m['total'], 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No invoices found for the selected period.</td></tr>
                    @endforelse
                </tbody>
                @if (count($months) > 0)
                <tfoot>
                    <tr class="bg-gray-50 font-semibold text-gray-900">
                        <td class="px-4 py-3">Grand Total</td>
                        <td class="px-4 py-3 text-right">{{ collect($months)->sum('count') }}</td>
                        <td class="px-4 py-3 text-right font-mono">{{ number_format($grandSubtotal, 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono text-amber-600">{{ number_format($grandTax, 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono">{{ number_format($grandTotal, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
