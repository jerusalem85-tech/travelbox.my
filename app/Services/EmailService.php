<?php

namespace App\Services;

use App\Mail\CustomEmailMail;
use App\Mail\TripDetailMail;
use App\Models\EmailLog;
use App\Models\Trip;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class EmailService
{
    public function sendTripDetail(Trip $trip, string $to, string $customMessage = ''): EmailLog
    {
        try {
            $mailable = new TripDetailMail($trip, $customMessage);
            $mailable->to($to);
            Mail::send($mailable);

            return $this->log($trip, $trip->customer, $to, $mailable->envelope()->subject, $customMessage ?: 'Trip details sent', 'trip_detail', 'sent');
        } catch (\Exception $e) {
            Log::error('Email send failed: ' . $e->getMessage());
            return $this->log($trip, $trip->customer, $to, "Trip Details - {$trip->trip_number}", $customMessage ?: 'Trip details', 'trip_detail', 'failed', $e->getMessage());
        }
    }

    public function sendCustom(Trip $trip, string $to, string $subject, string $body): EmailLog
    {
        try {
            $mailable = new CustomEmailMail($subject, $body, $trip->trip_number);
            $mailable->to($to);
            Mail::send($mailable);

            return $this->log($trip, $trip->customer, $to, $subject, $body, 'custom', 'sent');
        } catch (\Exception $e) {
            Log::error('Email send failed: ' . $e->getMessage());
            return $this->log($trip, $trip->customer, $to, $subject, $body, 'custom', 'failed', $e->getMessage());
        }
    }

    protected function log(?Trip $trip, $customer, string $to, string $subject, string $body, string $type, string $status, ?string $error = null): EmailLog
    {
        $log = new EmailLog([
            'trip_id' => $trip?->id,
            'customer_id' => $customer?->id,
            'to' => $to,
            'subject' => $subject,
            'body' => $body,
            'type' => $type,
            'status' => $status,
            'error_message' => $error,
            'sent_by' => auth()->id(),
        ]);
        $log->save();

        if ($trip) {
            $notifService = app(NotificationService::class);
            if ($status === 'sent') {
                $trip->logTimeline('email_sent', "Email sent to {$to}: {$subject}");
                $notifService->emailSent($to, $subject, $trip);
            } else {
                $notifService->emailFailed($to, $subject, $trip);
            }
        }

        return $log;
    }
}
