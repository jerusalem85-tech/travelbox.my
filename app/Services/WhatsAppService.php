<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\WhatsAppLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected ?string $idInstance;
    protected ?string $apiToken;
    protected string $apiUrl;

    public function __construct()
    {
        $this->idInstance = config('services.green_api.id_instance');
        $this->apiToken = config('services.green_api.api_token');
        $this->apiUrl = "https://api.greenapi.com/waInstance{$this->idInstance}/sendMessage/{$this->apiToken}";
    }

    public function isConfigured(): bool
    {
        return !empty($this->idInstance) && !empty($this->apiToken);
    }

    public function sendTripDetail(Trip $trip, string $phone, string $customMessage = ''): WhatsAppLog
    {
        $message = $this->formatTripDetail($trip, $customMessage);
        return $this->send($trip, $trip->customer, $phone, $message, 'trip_detail');
    }

    public function sendCustom(Trip $trip, string $phone, string $message): WhatsAppLog
    {
        return $this->send($trip, $trip->customer, $phone, $message, 'custom');
    }

    protected function send(?Trip $trip, $customer, string $phone, string $message, string $type = 'custom'): WhatsAppLog
    {
        try {
            $chatId = $this->normalizePhone($phone);

            if ($this->isConfigured()) {
                $response = Http::timeout(15)->post($this->apiUrl, [
                    'chatId' => $chatId,
                    'message' => $message,
                ]);

                $body = $response->json();
                $success = $response->successful() && ($body['idMessage'] ?? false);
                $messageId = $body['idMessage'] ?? null;
                $error = $success ? null : ($body['error'] ?? 'Unknown API error');
            } else {
                $messageId = 'simulated-' . now()->timestamp;
                $success = true;
                $error = null;
            }

            $status = $success ? 'sent' : 'failed';

            if ($success && $trip) {
                $notifService = app(NotificationService::class);
                $trip->logTimeline('whatsapp_sent', "WhatsApp sent to {$phone}" . ($type === 'trip_detail' ? ' (trip details)' : ''));
                $notifService->whatsappSent($phone, $trip);
            } elseif (!$success && $trip) {
                app(NotificationService::class)->whatsappFailed($phone, $trip);
            }

            return $this->log($trip, $customer, $phone, $message, $type, $status, $error, $messageId);
        } catch (\Exception $e) {
            Log::error('WhatsApp send failed: ' . $e->getMessage());

            if ($trip) {
                app(NotificationService::class)->whatsappFailed($phone, $trip);
            }

            return $this->log($trip, $customer, $phone, $message, $type, 'failed', $e->getMessage());
        }
    }

    protected function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($digits) > 0 && $digits[0] === '0') {
            $digits = substr($digits, 1);
        }
        return $digits . '@c.us';
    }

    protected function formatTripDetail(Trip $trip, string $customMessage = ''): string
    {
        $lines = [];

        if ($customMessage) {
            $lines[] = $customMessage;
            $lines[] = '';
        }

        $lines[] = "Trip: {$trip->trip_number}";
        if ($trip->name) $lines[] = "Name: {$trip->name}";
        $lines[] = "Destination: " . ($trip->destination ?: 'N/A');
        $lines[] = "Dates: " . ($trip->start_date?->format('M d, Y') ?: '?') . " - " . ($trip->end_date?->format('M d, Y') ?: '?');
        $lines[] = "Status: " . str_replace('_', ' ', ucfirst($trip->status));

        if ($trip->flightSegments->isNotEmpty()) {
            $lines[] = '';
            $lines[] = 'Flights:';
            foreach ($trip->flightSegments as $fs) {
                $lines[] = "  {$fs->airline} {$fs->flight_number} {$fs->departure_airport}->{$fs->arrival_airport} " . ($fs->departure_datetime?->format('M d H:i') ?: '');
            }
        }

        if ($trip->hotelBookings->isNotEmpty()) {
            $lines[] = '';
            $lines[] = 'Hotels:';
            foreach ($trip->hotelBookings as $hb) {
                $lines[] = "  {$hb->hotel_name} ({$hb->city}) " . ($hb->check_in?->format('M d') ?: '') . " - " . ($hb->check_out?->format('M d') ?: '');
            }
        }

        if ($trip->transferBookings->isNotEmpty()) {
            $lines[] = '';
            $lines[] = 'Transfers:';
            foreach ($trip->transferBookings as $tb) {
                $lines[] = "  {$tb->pickup_location} -> {$tb->dropoff_location} " . ($tb->pickup_datetime?->format('M d H:i') ?: '');
            }
        }

        if ($trip->passengers->isNotEmpty()) {
            $lines[] = '';
            $lines[] = 'Passengers:';
            foreach ($trip->passengers as $p) {
                $lines[] = "  {$p->first_name} {$p->last_name}";
            }
        }

        $lines[] = '';
        $lines[] = "Total: {$trip->currency} " . number_format($trip->total_selling_price, 2);

        return implode("\n", $lines);
    }

    protected function log(?Trip $trip, $customer, string $phone, string $message, string $type, string $status, ?string $error = null, ?string $messageId = null): WhatsAppLog
    {
        $log = new WhatsAppLog([
            'trip_id' => $trip?->id,
            'customer_id' => $customer?->id,
            'to' => $phone,
            'message' => $message,
            'type' => $type,
            'status' => $status,
            'error_message' => $error,
            'green_api_message_id' => $messageId,
            'sent_by' => auth()->id(),
        ]);
        $log->save();

        return $log;
    }
}
