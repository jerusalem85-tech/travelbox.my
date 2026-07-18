<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    protected $listeners = ['notifications-refreshed' => 'refreshCount'];

    public function getListeners(): array
    {
        return [
            'notifications-refreshed' => 'refreshCount',
        ];
    }

    public function mount(): void
    {
        $this->refreshCount();
    }

    public function refreshCount(): void
    {
        $user = auth()->user();
        $this->unreadCount = $user ? $user->unreadNotifications()->count() : 0;
    }

    public function markAsRead(string $notificationId): void
    {
        $user = auth()->user();
        if (!$user) return;
        $notification = $user->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->refreshCount();
        }
    }

    public function markAllAsRead(): void
    {
        $user = auth()->user();
        if (!$user) return;
        $user->unreadNotifications->markAsRead();
        $this->refreshCount();
    }

    public function render()
    {
        $user = auth()->user();
        return view('livewire.notification-bell', [
            'notifications' => $user ? $user->notifications()->latest()->take(10)->get() : collect(),
        ]);
    }
}
