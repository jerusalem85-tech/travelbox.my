<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\User;
use App\Notifications\AppNotification;

class NotificationService
{
    public function tripStatusChanged(Trip $trip, string $oldStatus, string $newStatus): void
    {
        $message = "Trip {$trip->trip_number} status changed from " . str_replace('_', ' ', $oldStatus) . " to " . str_replace('_', ' ', $newStatus);
        $url = route('trips.show', $trip);
        $this->notifyUsers($message, $url, 'trip_status');
    }

    public function paymentReceived(Trip $trip, float $amount, string $currency): void
    {
        $message = "Payment of {$currency} " . number_format($amount, 2) . " received for trip {$trip->trip_number}";
        $url = route('trips.show', $trip);
        $this->notifyUsers($message, $url, 'payment');
    }

    public function invoiceCreated(Trip $trip, string $invoiceNumber): void
    {
        $message = "Invoice {$invoiceNumber} created for trip {$trip->trip_number}";
        $url = route('trips.show', $trip);
        $this->notifyUsers($message, $url, 'invoice');
    }

    public function taskDueSoon(Trip $trip, string $taskDescription, \Carbon\Carbon $dueDate): void
    {
        $message = "Task \"{$taskDescription}\" for trip {$trip->trip_number} is due " . $dueDate->diffForHumans();
        $url = route('trips.show', $trip);
        $this->notifyUsers($message, $url, 'task');
    }

    public function taskOverdue(Trip $trip, string $taskDescription): void
    {
        $message = "Task \"{$taskDescription}\" for trip {$trip->trip_number} is overdue!";
        $url = route('trips.show', $trip);
        $this->notifyUsers($message, $url, 'task');
    }

    public function emailSent(string $recipient, string $subject, ?Trip $trip = null): void
    {
        $message = "Email sent to {$recipient}: {$subject}";
        $url = $trip ? route('trips.show', $trip) : '';
        $this->notifyUsers($message, $url, 'email');
    }

    public function emailFailed(string $recipient, string $subject, ?Trip $trip = null): void
    {
        $message = "Email FAILED to {$recipient}: {$subject}";
        $url = $trip ? route('trips.show', $trip) : '';
        $this->notifyUsers($message, $url, 'email');
    }

    public function whatsappSent(string $phone, ?Trip $trip = null): void
    {
        $message = "WhatsApp sent to {$phone}";
        $url = $trip ? route('trips.show', $trip) : '';
        $this->notifyUsers($message, $url, 'whatsapp');
    }

    public function whatsappFailed(string $phone, ?Trip $trip = null): void
    {
        $message = "WhatsApp FAILED to {$phone}";
        $url = $trip ? route('trips.show', $trip) : '';
        $this->notifyUsers($message, $url, 'whatsapp');
    }

    public function fileUploaded(Trip $trip, string $filename): void
    {
        $message = "File \"{$filename}\" uploaded to trip {$trip->trip_number}";
        $url = route('trips.show', $trip);
        $this->notifyUsers($message, $url, 'file');
    }

    protected function notifyUsers(string $message, string $url, string $type): void
    {
        $users = User::all();
        foreach ($users as $user) {
            $user->notify(new AppNotification($type, $message, $url));
        }
    }
}
