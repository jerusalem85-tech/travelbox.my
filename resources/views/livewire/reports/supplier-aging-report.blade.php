<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Supplier Aging Report</h2>
        <p class="text-sm text-gray-500 mt-1">Supplier payables and outstanding balances</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <div class="max-w-xs">
            <label class="block text-xs font-medium text-gray-500 mb-1">Search Supplier</label>
            <input type="text" wire:model.live="search" placeholder="Company name, contact, or email..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Supplier Payables</h3></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="px-4 py-3 font-medium">Supplier</th>
                        <th class="px-4 py-3 font-medium text-right">Total Cost</th>
                        <th class="px-4 py-3 font-medium text-right">Total Paid</th>
                        <th class="px-4 py-3 font-medium text-right">Balance</th>
                        <th class="px-4 py-3 font-medium">Last Booking</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $s)
                    <tr class="border-b border-gray-100">
                        <td class="px-4 py-3">
                            <a href="{{ route('suppliers.show', $s['id']) }}" class="text-blue-600 hover:underline font-medium">{{ $s['company_name'] }}</a>
                            @if ($s['contact_person'])
                            <div class="text-xs text-gray-400">{{ $s['contact_person'] }}</div>
                            @endif
                            <div class="text-xs text-gray-400">{{ $s['email'] }}</div>
                        </td>
                        <td class="px-4 py-3 text-right font-mono">{{ number_format($s['total_cost'], 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono">{{ number_format($s['total_paid'], 2) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-medium {{ $s['balance'] > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($s['balance'], 2) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $s['last_booking'] ? \Carbon\Carbon::parse($s['last_booking'])->format('M d, Y') : '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No suppliers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
