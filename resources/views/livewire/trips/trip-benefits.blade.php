<div>
    <div class="flex items-center gap-2 mb-3">
        <button wire:click="openForm" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Benefit
        </button>
    </div>

    @if ($showForm)
    <div class="mb-4 p-4 bg-amber-50/50 rounded-lg border border-amber-200">
        <form wire:submit="save" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700">Type</label>
                    <select wire:model="type" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                        <option value="">Select type...</option>
                        <option value="lounge">Lounge Access</option>
                        <option value="fast_track">Fast Track</option>
                        <option value="meet_assist">Meet & Assist</option>
                        <option value="vip">VIP Service</option>
                        <option value="insurance">Travel Insurance</option>
                        <option value="visa">Visa Service</option>
                        <option value="hotel_upgrade">Hotel Upgrade</option>
                        <option value="special_occasion">Special Occasion</option>
                        <option value="other">Other</option>
                    </select>
                    @error('type') <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Description *</label>
                    <input type="text" wire:model="description" placeholder="e.g. VIP lounge at AMM" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                    @error('description') <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700">Provider</label>
                    <input type="text" wire:model="provider" placeholder="e.g. Royal Jordanian" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Cost</label>
                    <input type="number" wire:model="cost" step="0.01" min="0" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Selling Price</label>
                    <input type="number" wire:model="selling_price" step="0.01" min="0" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700">Currency</label>
                    <select wire:model="currency" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                        <option value="">Select...</option>
                        <option value="USD">USD</option>
                        <option value="ILS">ILS</option>
                        <option value="JOD">JOD</option>
                        <option value="EUR">EUR</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Notes</label>
                    <input type="text" wire:model="notes" placeholder="Any additional info" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-3 py-1.5 text-xs font-medium text-white bg-amber-600 rounded hover:bg-amber-700">{{ $editingId ? 'Update' : 'Save' }}</button>
                <button type="button" wire:click="cancel" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Cancel</button>
            </div>
        </form>
    </div>
    @endif

    @if ($trip->benefits->isEmpty())
        <p class="text-xs text-gray-400 italic">No benefits added yet.</p>
    @else
        <div class="space-y-2">
            @foreach ($trip->benefits as $benefit)
            <div class="flex items-center justify-between p-2.5 bg-amber-50/30 rounded-lg border border-amber-100">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="shrink-0 w-6 h-6 rounded-full bg-amber-100 flex items-center justify-center">
                        @php $icons = ['lounge' => '🛋️', 'fast_track' => '⚡', 'meet_assist' => '🤝', 'vip' => '👑', 'insurance' => '🛡️', 'visa' => '📄', 'hotel_upgrade' => '🏨', 'special_occasion' => '🎉', 'other' => '✨']; @endphp
                        <span class="text-xs">{{ $icons[$benefit->type] ?? '✦' }}</span>
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ $benefit->description }}</p>
                        <p class="text-xs text-gray-500">
                            {{ ucwords(str_replace('_', ' ', $benefit->type)) }}
                            @if ($benefit->provider) &middot; {{ $benefit->provider }} @endif
                            @if ($benefit->cost) &middot; Cost: {{ number_format($benefit->cost, 2) }} @endif
                            @if ($benefit->selling_price) &middot; Price: {{ number_format($benefit->selling_price, 2) }} @endif
                            @if ($benefit->currency) {{ $benefit->currency }} @endif
                        </p>
                        @if ($benefit->notes)
                        <p class="text-xs text-gray-400 italic mt-0.5">{{ $benefit->notes }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <button wire:click="edit('{{ $benefit->id }}')" class="p-1 text-amber-500 hover:text-amber-700 hover:bg-amber-100 rounded" title="Edit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                    <button wire:click="remove('{{ $benefit->id }}')" wire:confirm="Remove this benefit?" class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded" title="Remove">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
