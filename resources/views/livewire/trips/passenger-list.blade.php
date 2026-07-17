<div>
    @if ($showForm)
    <div class="fixed inset-0 z-50 flex items-start justify-center pt-12" x-data x-on:keydown.escape.window="$wire.closeModal">
        <div class="fixed inset-0 bg-indigo-500/20" wire:click="closeModal"></div>
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[85vh] overflow-y-auto z-10">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between z-10">
                <h2 class="text-lg font-semibold text-gray-900">Manage Passengers ({{ $trip->passengers->count() }})</h2>
                <button wire:click="closeModal" class="p-1 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6">
                <button wire:click="openForm" class="mb-4 inline-flex items-center px-3 py-1.5 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Add Passenger
                </button>

                @if ($showForm)
                <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <form wire:submit="{{ $editingPassengerId ? 'update' : 'add' }}" class="space-y-3">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-3 {{ $passportPath ? 'bg-green-50 border-green-300' : 'bg-gray-50' }}">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <svg class="w-6 h-6 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5l-1-1H6l-1 1z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <div class="text-sm">
                                    @if ($passportPath)
                                        <p class="text-green-700 font-medium">✓ Passport scanned</p>
                                    @else
                                        <p class="text-gray-700 font-medium">Scan passport</p>
                                        <p class="text-xs text-gray-500 mt-0.5">Upload passport photo or PDF to auto-fill</p>
                                    @endif
                                    <input type="file" wire:model="passportUpload" accept="image/*,.pdf" class="hidden" />
                                </div>
                            </label>
                            @if ($passportPath)
                            <div class="mt-2 space-y-2">
                                <div class="flex items-center gap-2">
                                    @if (str_starts_with(Storage::disk('public')->mimeType($passportPath), 'image/'))
                                    <img src="{{ Storage::disk('public')->url($passportPath) }}" class="max-h-20 rounded border border-gray-200" />
                                    @else
                                    <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded border">PDF</span>
                                    @endif
                                    <button type="button" wire:click="removePassportUpload" class="text-xs text-red-600 hover:underline">Remove</button>
                                </div>

                                @if ($passportOcrStatus === 'idle')
                                <button type="button" wire:click="scanPassport" class="w-full py-1.5 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100 transition-colors flex items-center justify-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Scan Passport – Auto Fill Fields
                                </button>
                                @endif

                                @if ($passportOcrStatus === 'processing')
                                <div class="flex items-center gap-2 p-2 bg-blue-50 rounded border border-blue-200">
                                    <svg class="w-4 h-4 text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                                    <span class="text-xs text-blue-700 font-medium">Reading passport...</span>
                                </div>
                                @endif

                                @if ($passportOcrStatus === 'done' && !empty($passportOcrResults))
                                <div class="bg-white border border-emerald-200 rounded overflow-hidden">
                                    <div class="bg-emerald-50 px-2 py-1.5 border-b border-emerald-200 flex items-center justify-between">
                                        <span class="text-xs font-semibold text-emerald-700">Fields detected</span>
                                        <button type="button" wire:click="applyPassportOcr" class="text-xs font-medium text-white bg-emerald-600 px-2.5 py-1 rounded hover:bg-emerald-700 transition-colors">Apply</button>
                                    </div>
                                    <div class="p-2 space-y-1 text-xs text-gray-600">
                                        @if ($passportOcrResults['first_name'] ?? null) <div><span class="text-gray-400">Name:</span> {{ $passportOcrResults['first_name'] }} {{ $passportOcrResults['last_name'] ?? '' }}</div> @endif
                                        @if ($passportOcrResults['passport_number'] ?? null) <div><span class="text-gray-400">Passport:</span> {{ $passportOcrResults['passport_number'] }}</div> @endif
                                        @if ($passportOcrResults['nationality'] ?? null) <div><span class="text-gray-400">Nationality:</span> {{ $passportOcrResults['nationality'] }}</div> @endif
                                        @if ($passportOcrResults['date_of_birth'] ?? null) <div><span class="text-gray-400">DOB:</span> {{ $passportOcrResults['date_of_birth'] }}</div> @endif
                                        @if ($passportOcrResults['passport_expiry'] ?? null) <div><span class="text-gray-400">Expiry:</span> {{ $passportOcrResults['passport_expiry'] }}</div> @endif
                                    </div>
                                </div>

                                <details class="text-[10px]">
                                    <summary class="text-gray-400 cursor-pointer hover:text-gray-600">Raw text</summary>
                                    <pre class="mt-1 p-2 bg-gray-50 rounded border border-gray-200 text-[10px] text-gray-400 max-h-24 overflow-y-auto whitespace-pre-wrap">{{ $passportOcrResults['raw_text'] ?? '' }}</pre>
                                </details>
                                @endif

                                @if ($passportOcrStatus === 'error')
                                <div class="flex items-center gap-2 p-2 bg-red-50 rounded border border-red-200">
                                    <span class="text-xs text-red-700">OCR failed.</span>
                                    <button type="button" wire:click="scanPassport" class="text-xs text-red-600 hover:underline ml-auto">Retry</button>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700">First Name *</label>
                                <input type="text" wire:model="first_name" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                                @error('first_name') <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Last Name *</label>
                                <input type="text" wire:model="last_name" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                                @error('last_name') <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-700">Date of Birth</label><input type="date" wire:model="date_of_birth" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs font-medium text-gray-700">Nationality</label><input type="text" wire:model="nationality" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-700">Passport Number</label><input type="text" wire:model="passport_number" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs font-medium text-gray-700">Passport Expiry</label><input type="date" wire:model="passport_expiry" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div><label class="block text-xs font-medium text-gray-700">Passport Issue Date</label><input type="date" wire:model="passport_issue_date" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                            <div><label class="block text-xs font-medium text-gray-700">Passport Issue Place</label><input type="text" wire:model="passport_issue_place" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" /></div>
                        </div>

                        <div class="border-t border-gray-200 pt-3">
                            <p class="text-xs font-semibold text-gray-700 mb-2">Benefits & Preferences</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Meal Preference</label>
                                    <select wire:model="meal_preference" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                        <option value="">None</option>
                                        <option value="Regular">Regular</option>
                                        <option value="Vegetarian">Vegetarian</option>
                                        <option value="Vegan">Vegan</option>
                                        <option value="Halal">Halal</option>
                                        <option value="Kosher">Kosher</option>
                                        <option value="Diabetic">Diabetic</option>
                                        <option value="Gluten Free">Gluten Free</option>
                                        <option value="Lactose Free">Lactose Free</option>
                                        <option value="Child Meal">Child Meal</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Seat Preference</label>
                                    <select wire:model="seat_preference" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                        <option value="">None</option>
                                        <option value="Aisle">Aisle</option>
                                        <option value="Window">Window</option>
                                        <option value="Middle">Middle</option>
                                        <option value="Bulkhead">Bulkhead (extra legroom)</option>
                                        <option value="Exit Row">Exit Row</option>
                                        <option value="Front">Front</option>
                                        <option value="Together">Seat Together</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">Frequent Flyer (FFP)</label>
                                    <input type="text" wire:model="ffp_number" placeholder="Card number" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700">FFP Airline</label>
                                    <input type="text" wire:model="ffp_airline" placeholder="e.g. Emirates, Qatar, Royal Jordanian" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" />
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="block text-xs font-medium text-gray-700">Special Requests</label>
                                <textarea wire:model="special_requests" rows="2" placeholder="Wheelchair, room preferences, connecting rooms, etc." class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm"></textarea>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700">Link to Customer</label>
                            <select wire:model="customer_id" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                                <option value="">None</option>
                                @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="submit" class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">{{ $editingPassengerId ? 'Update' : 'Add' }}</button>
                            <button type="button" wire:click="$set('showForm', false)" class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">Cancel</button>
                        </div>
                    </form>
                </div>
                @endif

                @if ($trip->passengers->isEmpty())
                <p class="text-sm text-gray-500 text-center py-8">No passengers added yet.</p>
                @else
                <div class="space-y-2">
                    @foreach ($trip->passengers as $passenger)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-xs font-medium text-gray-600">{{ substr($passenger->first_name, 0, 1) }}{{ substr($passenger->last_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $passenger->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $passenger->nationality ?: '—' }} @if ($passenger->passport_number) &middot; {{ $passenger->passport_number }} @endif</p>
                                @if ($passenger->benefits_summary)
                                <p class="text-xs text-amber-600 mt-0.5">✦ {{ $passenger->benefits_summary }}</p>
                                @endif
                                @if ($passenger->special_requests)
                                <p class="text-xs text-gray-400 italic mt-0.5">{{ $passenger->special_requests }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <button wire:click="edit('{{ $passenger->id }}')" class="p-1 text-amber-500 hover:text-amber-700 hover:bg-amber-50 rounded" title="Edit"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg></button>
                            <button wire:click="remove('{{ $passenger->id }}')" wire:confirm="Remove {{ $passenger->full_name }}?" class="p-1 text-red-500 hover:text-red-700 hover:bg-red-50 rounded" title="Remove"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
