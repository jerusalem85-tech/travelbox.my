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
        $this->unreadCount = auth()->user()->unreadNotifications()->count();
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->refreshCount();
        }
    }

    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->refreshCount();
    }

    public function render()
    {
        return view('livewire.notification-bell', [
            'notifications' => auth()->user()->notifications()->latest()->take(10)->get(),
        ]);
    }
}
