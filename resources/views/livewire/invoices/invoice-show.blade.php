<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Invoice {{ $invoice->invoice_number }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ ucfirst($invoice->type) }} &middot; {{ $invoice->issue_date?->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('invoices.edit', $invoice) }}" class="px-3 py-2 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100">Edit</a>
            <a href="{{ route('pdfs.invoice', $invoice) }}" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                PDF
            </a>
            <a href="{{ route('invoices.index') }}" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Items</h3></div>
                <div class="p-5">
                    <table class="w-full text-sm">
                        <thead><tr class="text-left text-gray-500 border-b border-gray-200">
                            <th class="pb-2 font-medium">Description</th><th class="pb-2 font-medium text-right">Qty</th><th class="pb-2 font-medium text-right">Unit Price</th><th class="pb-2 font-medium text-right">Total</th>
                        </tr></thead>
                        <tbody>
                            @foreach ($invoice->items as $item)
                            <tr class="border-b border-gray-100">
                                <td class="py-2 text-gray-900">{{ $item->description }}</td>
                                <td class="py-2 text-right text-gray-600">{{ $item->quantity }}</td>
                                <td class="py-2 text-right font-mono">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-2 text-right font-mono">{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Summary</h3></div>
                <div class="p-5 space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Customer</span><span class="text-gray-900 font-medium">{{ $invoice->customer?->full_name ?: '—' }}</span></div>
                    @if ($invoice->trip)<div class="flex justify-between"><span class="text-gray-500">Trip</span><span class="text-gray-900">{{ $invoice->trip->name }}</span></div>@endif
                    <div class="flex justify-between"><span class="text-gray-500">Status</span>
                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full {{ match($invoice->status) { 'paid' => 'bg-green-100 text-green-700', 'pending' => 'bg-yellow-100 text-yellow-700', 'draft' => 'bg-gray-100 text-gray-700', default => 'bg-gray-100 text-gray-700' } }}">{{ ucfirst($invoice->status) }}</span>
                    </div>
                    <hr class="border-gray-200" />
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="font-mono">{{ number_format($invoice->subtotal, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Tax</span><span class="font-mono">{{ number_format($invoice->tax, 2) }}</span></div>
                    <div class="flex justify-between font-semibold text-base"><span>Total</span><span class="font-mono">{{ number_format($invoice->total, 2) }}</span></div>
                </div>
            </div>

            @if ($invoice->notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Notes</h3>
                <p class="text-sm text-gray-700">{{ $invoice->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
