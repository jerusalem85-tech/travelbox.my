<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900">{{ $editing ? 'Edit' : 'Record' }} Payment</h2>
        <a href="{{ route('payments.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Back</a>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select wire:model="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1">
                        <option value="customer_payment">Customer Payment</option>
                        <option value="supplier_payment">Supplier Payment</option>
                        <option value="refund">Refund</option>
                        <option value="deposit">Deposit</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                    <select wire:model="payment_method" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1">
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cash">Cash</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="cheque">Cheque</option>
                        <option value="online">Online Payment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Amount *</label>
                    <input type="number" step="0.01" min="0" wire:model="amount" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1" />
                    @error('amount') <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Currency</label>
                    <select wire:model="currency" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1">
                        <option value="USD">USD</option><option value="ILS">ILS</option><option value="JOD">JOD</option><option value="EUR">EUR</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Payment Date *</label>
                    <input type="date" wire:model="payment_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1" />
                    @error('payment_date') <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select wire:model="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1">
                        <option value="completed">Completed</option><option value="pending">Pending</option><option value="failed">Failed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Trip (optional)</label>
                    <select wire:model="trip_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1">
                        <option value="">No trip</option>
                        @foreach ($trips as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->trip_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Invoice (optional)</label>
                    <select wire:model="invoice_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1">
                        <option value="">No invoice</option>
                        @foreach ($invoices as $inv)
                        <option value="{{ $inv->id }}">{{ $inv->invoice_number }} ({{ number_format($inv->total, 2) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Reference</label>
                    <input type="text" wire:model="reference" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Exchange Rate</label>
                    <input type="number" step="0.0001" wire:model="exchange_rate" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea wire:model="description" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1"></textarea>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">
                    <span wire:loading.remove wire:target="save">{{ $editing ? 'Update Payment' : 'Record Payment' }}</span>
                    <span wire:loading wire:target="save" class="flex items-center gap-2"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Saving...</span>
                </button>
            <a href="{{ route('payments.index') }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
