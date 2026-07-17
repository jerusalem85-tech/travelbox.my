<?php

namespace App\Livewire\Trips;

use App\Models\Trip;
use Livewire\Component;
use App\Services\WhatsAppService;

class TripWhatsApp extends Component
{
    public Trip $trip;
    public bool $showForm = false;
    public string $phone = '';
    public string $message = '';
    public bool $includeTripDetails = false;
    public string $statusMessage = '';

    protected function rules(): array
    {
        return [
            'phone' => 'required|string|max:20',
            'message' => 'required_without:includeTripDetails|string|max:10000',
        ];
    }

    public function mount(Trip $trip): void
    {
        $this->trip = $trip;
        $this->phone = $trip->customer?->mobile ?? $trip->customer?->phone ?? '';
    }

    public function send(): void
    {
        $this->validate();

        try {
            $service = app(WhatsAppService::class);

            if ($this->includeTripDetails) {
                $service->sendTripDetail($this->trip, $this->phone, $this->message);
            } else {
                $service->sendCustom($this->trip, $this->phone, $this->message);
            }

            $this->statusMessage = 'WhatsApp message sent successfully!';
            $this->reset(['message', 'showForm']);
            $this->dispatch('whatsapp-sent');
        } catch (\Exception $e) {
            $this->statusMessage = 'Failed to send: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.trips.trip-whatsapp', [
            'messages' => $this->trip->whatsappMessages()->latest()->take(10)->get(),
        ]);
    }
}
