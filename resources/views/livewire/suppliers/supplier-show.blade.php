<div class="space-y-6">
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center">
                <span class="text-xl font-bold text-purple-600">{{ substr($supplier->company_name, 0, 2) }}</span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $supplier->company_name }}</h2>
                <p class="text-sm text-gray-500">{{ $supplier->supplier_code }} &middot; {{ str_replace('_', ' ', ucfirst($supplier->type)) }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100">Edit</a>
            <a href="{{ route('suppliers.index') }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Contact Information</h3></div>
                <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><p class="text-gray-500">Contact Person</p><p class="text-gray-900">{{ $supplier->contact_person ?: '—' }}</p></div>
                    <div><p class="text-gray-500">Email</p><p class="text-gray-900">{{ $supplier->email ?: '—' }}</p></div>
                    <div><p class="text-gray-500">Phone</p><p class="text-gray-900">{{ $supplier->phone ?: '—' }}</p></div>
                    <div><p class="text-gray-500">Mobile</p><p class="text-gray-900">{{ $supplier->mobile ?: '—' }}</p></div>
                    <div class="sm:col-span-2"><p class="text-gray-500">Address</p><p class="text-gray-900">{{ $supplier->address ?: '—' }}</p></div>
                </div>
            </div>

            @if ($supplier->contract_notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Contract Notes</h3></div>
                <div class="p-5 text-sm text-gray-700 whitespace-pre-wrap">{{ $supplier->contract_notes }}</div>
            </div>
            @endif

            @if ($supplier->contacts->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Additional Contacts</h3></div>
                <div class="p-5">
                    @foreach ($supplier->contacts as $contact)
                        <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                            <div><p class="text-sm font-medium text-gray-900">{{ $contact->name }}</p><p class="text-xs text-gray-500">{{ $contact->email }} {{ $contact->phone ? '· ' . $contact->phone : '' }}</p></div>
                            <span class="text-xs text-gray-400 capitalize">{{ $contact->relationship }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200"><h3 class="text-sm font-semibold text-gray-900">Summary</h3></div>
                <div class="p-5 space-y-4 text-sm">
                    <div class="flex items-center justify-between"><span class="text-gray-500">Status</span>
                        <x-status-badge :status="$supplier->is_active ? 'active' : 'inactive'">{{ $supplier->is_active ? 'Active' : 'Inactive' }}</x-status-badge>
                    </div>
                    <div class="flex items-center justify-between"><span class="text-gray-500">Currency</span><span class="font-medium">{{ $supplier->preferred_currency }}</span></div>
                    <div class="flex items-center justify-between"><span class="text-gray-500">Payment Terms</span><span class="font-medium">{{ $supplier->payment_terms ?: '—' }}</span></div>
                    <div class="flex items-center justify-between"><span class="text-gray-500">Balance</span><span class="font-mono">{{ number_format($supplier->current_balance, 2) }}</span></div>
                    <div class="flex items-center justify-between"><span class="text-gray-500">Created</span><span class="text-gray-600">{{ $supplier->created_at->format('M d, Y') }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
