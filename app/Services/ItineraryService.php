<?php

namespace App\Services;

use App\Models\Trip;
use Barryvdh\DomPDF\Facade\Pdf;

class ItineraryService
{
    public function generatePdf(Trip $trip): \Barryvdh\DomPDF\PDF
    {
        $trip->load([
            'customer', 'passengers',
            'flightSegments.supplier',
            'hotelBookings.supplier',
            'transferBookings',
            'visaApplications',
            'insurancePolicies',
            'activities',
            'cruiseBookings',
            'trainBookings',
            'carRentals',
            'packageBookings',
            'otherServices',
        ]);

        $pdf = Pdf::loadView('pdfs.itinerary', [
            'trip' => $trip,
        ]);

        $pdf->setPaper('a4');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf;
    }

    public function generateHtml(Trip $trip): string
    {
        $trip->load([
            'customer', 'passengers',
            'flightSegments.supplier',
            'hotelBookings.supplier',
            'transferBookings',
            'visaApplications',
            'insurancePolicies',
            'activities',
            'cruiseBookings',
            'trainBookings',
            'carRentals',
            'packageBookings',
            'otherServices',
        ]);

        return view('pdfs.itinerary', [
            'trip' => $trip,
        ])->render();
    }
}
