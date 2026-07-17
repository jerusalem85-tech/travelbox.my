<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Payments</h2>
            <p class="text-sm text-gray-500 mt-1">Track all incoming and outgoing payments</p>
        </div>
        <a href="{{ route('payments.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">+ Record Payment</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <div class="flex flex-wrap gap-3">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search payments..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-64" />
                <select wire:model.live="category" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">All Categories</option>
                    <option value="customer_payment">Customer Payment</option>
                    <option value="supplier_payment">Supplier Payment</option>
                    <option value="refund">Refund</option>
                    <option value="deposit">Deposit</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b border-gray-200">
                        <th class="px-4 py-3 font-medium">Payment #</th>
                        <th class="px-4 py-3 font-medium">Date</th>
                        <th class="px-4 py-3 font-medium">Category</th>
                        <th class="px-4 py-3 font-medium">Description</th>
                        <th class="px-4 py-3 font-medium text-right">Amount</th>
                        <th class="px-4 py-3 font-medium">Method</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs">{{ $payment->payment_number }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $payment->payment_date?->format('M d, Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                {{ str_replace('_', ' ', ucfirst($payment->category)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-900 max-w-xs truncate">{{ $payment->description ?: '—' }}</td>
                        <td class="px-4 py-3 font-mono text-right">{{ number_format($payment->amount, 2) }} <span class="text-xs text-gray-400">{{ $payment->currency }}</span></td>
                        <td class="px-4 py-3 text-gray-600 capitalize">{{ $payment->payment_method ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $payment->status === 'completed' ? 'bg-green-100 text-green-700' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('pdfs.receipt', $payment) }}" target="_blank" class="text-red-600 hover:text-red-800 text-xs font-medium" title="Receipt PDF">Receipt</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No payments recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($payments->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">{{ $payments->links() }}</div>
        @endif
    </div>
</div>
