<div>
    <div class="space-y-3">
        @if ($trip->documents->isNotEmpty())
        <div class="space-y-2">
            @foreach ($trip->documents as $doc)
            <a href="{{ $doc->file_path ? Storage::disk('public')->url($doc->file_path) : '#' }}" target="_blank" class="flex items-center justify-between p-2.5 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors group {{ $doc->file_path ? '' : 'pointer-events-none' }}">
                <div class="flex items-center gap-2.5 min-w-0">
                    <svg class="w-4 h-4 shrink-0 {{ $doc->file_path ? 'text-blue-500' : 'text-gray-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                    <div class="min-w-0">
                        <p class="text-sm font-medium {{ $doc->file_path ? 'text-blue-700 group-hover:text-blue-900' : 'text-gray-400' }} truncate">{{ $doc->title }}</p>
                        <p class="text-xs text-gray-400">{{ str_replace('_', ' ', ucfirst($doc->type)) }} &middot; @if ($doc->size){{ round($doc->size / 1024) }} KB @endif</p>
                    </div>
                </div>
                <div class="flex items-center gap-1 shrink-0" onclick="event.stopPropagation();">
                    @if ($doc->file_path)
                    <a href="{{ Storage::disk('public')->url($doc->file_path) }}" target="_blank" class="p-1 text-gray-400 hover:text-blue-600" title="Download">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    </a>
                    @endif
                    <button wire:click="deleteFile('{{ $doc->id }}')" wire:confirm="Delete this file?" class="p-1 text-gray-400 hover:text-red-600" title="Delete">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-500 text-center py-4">No files uploaded yet.</p>
        @endif

        <form wire:submit="uploadFile" class="space-y-3 border-t border-gray-200 pt-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">File</label>
                <input type="file" wire:model="upload" class="w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 file:border file:border-blue-200 file:rounded-lg hover:file:bg-blue-100" />
                @error('upload') <p class="text-xs text-red-600 mt-0.5">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                    <select wire:model="category" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm">
                        <option value="ticket">Ticket</option>
                        <option value="hotel_voucher">Hotel Voucher</option>
                        <option value="service_voucher">Service Voucher</option>
                        <option value="itinerary">Itinerary</option>
                        <option value="visa_letter">Visa Letter</option>
                        <option value="insurance_certificate">Insurance Certificate</option>
                        <option value="invoice">Invoice</option>
                        <option value="receipt">Receipt</option>
                        <option value="contract">Contract</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" wire:model="title" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm" placeholder="Optional" />
                </div>
            </div>
            <button type="submit" class="w-full px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Upload</button>
        </form>
    </div>
</div>
