<div>
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-500">Total: <span class="font-medium text-gray-900">{{ number_format($trip->expenses->sum('amount'), 2) }}</span></p>
            <button wire:click="openForm" class="text-xs text-blue-600 hover:underline">+ Add</button>
        </div>

        @if ($showForm)
        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
            <form wire:submit="save" class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700">Description *</label>
                    <input type="text" wire:model="description" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Category</label>
                        <select wire:model="category" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                            <option value="transport">Transport</option>
                            <option value="meals">Meals</option>
                            <option value="accommodation">Accommodation</option>
                            <option value="visa">Visa Fees</option>
                            <option value="communication">Communication</option>
                            <option value="supplies">Supplies</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Amount *</label>
                        <input type="number" step="0.01" min="0" wire:model="amount" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Currency</label>
                        <select wire:model="currency" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                            <option value="USD">USD</option><option value="ILS">ILS</option><option value="JOD">JOD</option><option value="EUR">EUR</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Date</label>
                        <input type="date" wire:model="expense_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">{{ $editingId ? 'Update' : 'Add' }}</button>
                    <button type="button" wire:click="resetForm" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Cancel</button>
                </div>
            </form>
        </div>
        @endif

        @if ($trip->expenses->isEmpty())
        <p class="text-sm text-gray-500 text-center py-3">No expenses recorded.</p>
        @else
        <div class="space-y-2">
            @foreach ($trip->expenses as $expense)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900">{{ $expense->description }}</p>
                    <p class="text-xs text-gray-500">{{ str_replace('_', ' ', ucfirst($expense->category)) }} &middot; {{ $expense->expense_date?->format('M d, Y') ?: '—' }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-sm font-mono font-medium">{{ number_format($expense->amount, 2) }} {{ $expense->currency }}</span>
                    <button wire:click="edit('{{ $expense->id }}')" class="p-1 text-gray-400 hover:text-amber-600"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></button>
                    <button wire:click="delete('{{ $expense->id }}')" wire:confirm="Delete this expense?" class="p-1 text-gray-400 hover:text-red-600"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
