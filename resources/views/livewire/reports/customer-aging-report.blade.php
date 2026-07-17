<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Customer Aging Report</h2>
        <p class="text-sm text-gray-500 mt-1">Outstanding customer balances and overdue invoices</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="max-w-xs">
            <label class="block text-xs font-medium text-gray-500 mb-1">Search Customer</label>
            <input type="text" wire:model.live="search" placeholder="Name, company, or email..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Customer Balances</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="px-4 py-3 font-medium">Customer</th>
                        <th class="px-4 py-3 font-medium text-right">Total Invoiced</th>
                        <th class="px-4 py-3 font-medium text-right">Total Paid</th>
                        <th class="px-4 py-3 font-medium text-right">Balance</th>
                        <th class="px-4 py-3 font-medium text-right">Overdue Invoices</th>
                        <th class="px-4 py-3 font-medium">Last Activity</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $c)
                    <tr class="border-b border-gray-100">
                        <td class="px-4 py-3">
                            <a href="{{ route('customers.show', $c['id']) }}" class="text-blue-600 hover:underline font-medium">{{ $c['name'] }}</a>
                            @if ($c['company'])
                            <div class="text-xs text-gray-400">{{ $c['company'] }}</div>
                            @endif
                            <div class="text-xs text-gray-400">{{ $c['email'] }}</div>
                        </td>
                        <td class="px-4 py-3 text-right font-mono">{{ number_format($c['total_invoiced'], 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono">{{ number_format($c['total_paid'], 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-medium {{ $c['balance'] > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($c['balance'], 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            @if ($c['overdue_invoices'] > 0)
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">{{ $c['overdue_invoices'] }}</span>
                            @else
                            <span class="text-gray-400">0</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $c['last_activity'] ? \Carbon\Carbon::parse($c['last_activity'])->format('M d, Y') : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
