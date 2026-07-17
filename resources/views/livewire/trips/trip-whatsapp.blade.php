<div>
    <div class="flex items-center justify-between mb-3">
        <h4 class="text-sm font-medium text-gray-900">WhatsApp ({{ isset($messages) ? $messages->count() : 0 }})</h4>
        <button wire:click="$set('showForm', true)" class="text-xs text-green-600 hover:underline">+ Send WhatsApp</button>
    </div>

    @if ($statusMessage)
    <div class="mb-3 px-3 py-2 text-sm rounded-lg {{ str_contains($statusMessage, 'success') ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
        {{ $statusMessage }}
    </div>
    @endif

    @if ($showForm)
    <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200 space-y-3">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Phone Number (with country code)</label>
            <input type="text" wire:model="phone" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500" placeholder="e.g. 201234567890">
            @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Message</label>
            <textarea wire:model="message" rows="4" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500" placeholder="Write your message..." {{ $includeTripDetails ? 'disabled' : '' }}></textarea>
            @error('message') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" wire:model="includeTripDetails" class="rounded border-gray-300">
            Include trip details (flights, hotels, transfers, etc.)
        </label>
        <div class="flex gap-2">
            <button wire:click="send" wire:loading.attr="disabled" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 disabled:opacity-50">
                <span wire:loading.remove><svg class="w-4 h-4 inline mr-1 -mt-0.5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>Send</span>
                <span wire:loading>Sending...</span>
            </button>
            <button wire:click="$set('showForm', false)" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Cancel</button>
        </div>
    </div>
    @endif

    @if (isset($messages) && $messages->isNotEmpty())
    <div class="space-y-2">
        @foreach ($messages as $msg)
        <div class="flex items-start gap-2 p-2 rounded-lg {{ $msg->status === 'failed' ? 'bg-red-50' : 'bg-green-50' }}">
            <div class="shrink-0 mt-0.5">
                <svg class="w-4 h-4 {{ $msg->status === 'failed' ? 'text-red-400' : 'text-green-500' }}" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs text-gray-700 line-clamp-2">{{ Str::limit($msg->message, 120) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">To: {{ $msg->to }} &middot; {{ $msg->created_at->diffForHumans() }}</p>
                @if ($msg->status === 'failed' && $msg->error_message)
                <p class="text-xs text-red-500 mt-1">{{ $msg->error_message }}</p>
                @endif
            </div>
            <span class="shrink-0 text-xs {{ $msg->status === 'sent' ? 'text-green-600' : 'text-red-600' }}">{{ ucfirst($msg->status) }}</span>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-xs text-gray-400 text-center py-3">No WhatsApp messages sent yet</p>
    @endif
</div>
