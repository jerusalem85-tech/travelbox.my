<div>
    <div class="flex items-center justify-between mb-3">
        <h4 class="text-sm font-medium text-gray-900">Email Log ({{ $emails->count() }})</h4>
        <button wire:click="$set('showForm', true)" class="text-xs text-blue-600 hover:underline">+ Send Email</button>
    </div>

    @if ($statusMessage)
    <div class="mb-3 px-3 py-2 text-sm rounded-lg {{ str_contains($statusMessage, 'success') ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
        {{ $statusMessage }}
    </div>
    @endif

    @if ($showForm)
    <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200 space-y-3">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">To</label>
            <input type="email" wire:model="recipient" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            @error('recipient') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Subject</label>
            <input type="text" wire:model="subject" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Email subject">
            @error('subject') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Message</label>
            <textarea wire:model="body" rows="4" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Write your message..."></textarea>
            @error('body') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" wire:model="includeTripDetails" class="rounded border-gray-300">
            Include trip details (flights, hotels, transfers, etc.)
        </label>
        <div class="flex gap-2">
            <button wire:click="send" wire:loading.attr="disabled" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 disabled:opacity-50">
                <span wire:loading.remove>Send</span>
                <span wire:loading>Sending...</span>
            </button>
            <button wire:click="$set('showForm', false)" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Cancel</button>
        </div>
    </div>
    @endif

    @if ($emails->isNotEmpty())
    <div class="space-y-2">
        @foreach ($emails as $email)
        <div class="flex items-start gap-2 p-2 rounded-lg {{ $email->status === 'failed' ? 'bg-red-50' : 'bg-gray-50' }}">
            <div class="shrink-0 mt-0.5">
                <svg class="w-4 h-4 {{ $email->status === 'failed' ? 'text-red-400' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-medium text-gray-700 truncate">{{ $email->subject }}</p>
                <p class="text-xs text-gray-400">To: {{ $email->to }} &middot; {{ $email->created_at->diffForHumans() }}</p>
                @if ($email->status === 'failed' && $email->error_message)
                <p class="text-xs text-red-500 mt-1">{{ $email->error_message }}</p>
                @endif
            </div>
            <span class="shrink-0 text-xs {{ $email->status === 'sent' ? 'text-green-600' : 'text-red-600' }}">{{ ucfirst($email->status) }}</span>
        </div>
        @endforeach
    </div>
    @else
    <p class="text-xs text-gray-400 text-center py-3">No emails sent yet</p>
    @endif
</div>
