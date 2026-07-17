<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900">{{ $editing ? 'Edit' : 'New' }} Invoice</h2>
        <a href="{{ route('invoices.index') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Back</a>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Customer *</label>
                    <select wire:model="customer_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1">
                        <option value="">Select Customer</option>
                        @foreach ($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->full_name }} @if($c->company_name)({{ $c->company_name }})@endif</option>
                        @endforeach
                    </select>
                    @error('customer_id') <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p> @enderror
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
                    <label class="block text-sm font-medium text-gray-700">Type</label>
                    <select wire:model="type" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1">
                        <option value="sales">Sales</option><option value="credit_note">Credit Note</option><option value="proforma">Proforma</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select wire:model="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1">
                        <option value="draft">Draft</option><option value="pending">Pending</option><option value="paid">Paid</option><option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Issue Date *</label>
                    <input type="date" wire:model="issue_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1" />
                    @error('issue_date') <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Due Date *</label>
                    <input type="date" wire:model="due_date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1" />
                    @error('due_date') <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Invoice Items</h3>
                <button type="button" wire:click="addItem" class="text-xs text-blue-600 hover:underline">+ Add Item</button>
            </div>
            @error('items') <p class="text-xs text-red-600 mb-2">{{ $message }}</p> @enderror
            <div class="space-y-3">
                @foreach ($items as $index => $item)
                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <input type="text" wire:model="items.{{ $index }}.description" placeholder="Description" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                        @error("items.{$index}.description") <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p> @enderror
                    </div>
                    <div class="w-20">
                        <input type="number" wire:model="items.{{ $index }}.quantity" min="1" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                    </div>
                    <div class="w-28">
                        <input type="number" step="0.01" wire:model="items.{{ $index }}.unit_price" min="0" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                    </div>
                    <div class="w-24 pt-1.5 text-sm font-mono text-right">{{ number_format(((float) $item['unit_price']) * ((int) $item['quantity']), 2) }}</div>
                    <button type="button" wire:click="removeItem({{ $index }})" class="pt-1.5 text-gray-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                </div>
                @endforeach
            </div>

            <div class="border-t border-gray-200 mt-4 pt-4 flex justify-end">
                <div class="w-48 space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="font-mono">{{ number_format(collect($items)->sum(fn($i) => (float)$i['unit_price'] * (int)$i['quantity']), 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Tax</span><span class="font-mono">0.00</span></div>
                    <div class="flex justify-between font-semibold border-t border-gray-200 pt-1"><span>Total</span><span class="font-mono">{{ number_format(collect($items)->sum(fn($i) => (float)$i['unit_price'] * (int)$i['quantity']), 2) }}</span></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <label class="block text-sm font-medium text-gray-700">Notes</label>
            <textarea wire:model="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-1"></textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">
                    <span wire:loading.remove wire:target="save">{{ $editing ? 'Update Invoice' : 'Create Invoice' }}</span>
                    <span wire:loading wire:target="save" class="flex items-center gap-2"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Saving...</span>
                </button>
            <a href="{{ route('invoices.index') }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
