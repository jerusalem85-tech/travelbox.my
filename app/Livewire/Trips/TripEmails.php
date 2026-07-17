<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use Livewire\Component;
use App\Services\EmailService;

class TripEmails extends Component
{
    public Trip $trip;
    public bool $showForm = false;
    public string $recipient = '';
    public string $subject = '';
    public string $body = '';
    public bool $includeTripDetails = false;
    public string $statusMessage = '';

    protected function rules(): array
    {
        return [
            'recipient' => 'required|email',
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
        ];
    }

    public function mount(Trip $trip): void
    {
        $this->trip = $trip;
        $this->recipient = $trip->customer?->email ?? '';
    }

    public function send(): void
    {
        $this->validate();

        try {
            $service = app(EmailService::class);

            if ($this->includeTripDetails) {
                $service->sendTripDetail($this->trip, $this->recipient, $this->body);
            } else {
                $service->sendCustom($this->trip, $this->recipient, $this->subject, $this->body);
            }

            $this->statusMessage = 'Email sent successfully!';
            $this->reset(['subject', 'body', 'showForm']);
            $this->dispatch('email-sent');
        } catch (\Exception $e) {
            $this->statusMessage = 'Failed to send email: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.trips.trip-emails', [
            'emails' => $this->trip->emails()->latest()->take(10)->get(),
        ]);
    }
}
