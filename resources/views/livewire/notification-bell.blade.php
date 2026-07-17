<div x-data="{ open: false }" class="relative" @click.outside="open = false" wire:poll.30s>
    <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
        @if ($unreadCount > 0)
        <span class="absolute top-1 right-1 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full">{{ min($unreadCount, 99) }}</span>
        @endif
    </button>

    <div x-show="open" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50 max-h-96 flex flex-col">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 shrink-0">
            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
            @if ($unreadCount > 0)
            <button wire:click="markAllAsRead" class="text-xs text-blue-600 hover:underline">Mark all as read</button>
            @endif
        </div>

        <div class="overflow-y-auto flex-1">
            @forelse ($notifications as $notification)
            <div class="flex items-start gap-3 px-4 py-3 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }} border-b border-gray-100 hover:bg-gray-50 transition-colors"
                wire:click="markAsRead('{{ $notification->id }}')"
                @if ($notification->data['url'] ?? false)
                onclick="window.Livewire.navigate('{{ $notification->data['url'] }}')"
                @endif
                style="cursor: pointer;">
                <div class="shrink-0 mt-0.5">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs
                        {{ match($notification->data['icon'] ?? 'bell') {
                            'trip_status' => 'bg-blue-500',
                            'payment' => 'bg-green-500',
                            'invoice' => 'bg-amber-500',
                            'task' => 'bg-purple-500',
                            'email' => 'bg-indigo-500',
                            'file' => 'bg-teal-500',
                            default => 'bg-gray-500',
                        } }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if (($notification->data['icon'] ?? 'bell') === 'trip_status')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            @elseif (($notification->data['icon'] ?? 'bell') === 'payment')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            @elseif (($notification->data['icon'] ?? 'bell') === 'task')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            @elseif (($notification->data['icon'] ?? 'bell') === 'email')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            @endif
                        </svg>
                    </div>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm text-gray-700">{{ $notification->data['message'] ?? '' }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="px-4 py-8 text-center text-sm text-gray-400">No notifications yet</div>
            @endforelse
        </div>
    </div>
</div>
