<?php

namespace App\Mail;

use App\Models\Trip;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TripDetailMail extends Mailable
{
    use Queueable;

    public function __construct(public Trip $trip, public string $customMessage = '')
    {
        $this->trip->loadMissing([
            'customer', 'passengers',
            'flightSegments', 'hotelBookings', 'transferBookings',
            'visaApplications', 'insurancePolicies', 'activities',
            'cruiseBookings', 'trainBookings', 'carRentals',
            'packageBookings', 'otherServices',
        ]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Trip Details - {$this->trip->trip_number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trip-detail',
        );
    }
}
