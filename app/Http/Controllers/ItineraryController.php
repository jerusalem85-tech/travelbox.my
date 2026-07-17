<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Services\ItineraryService;

class ItineraryController extends Controller
{
    public function view(Trip $trip)
    {
        $service = app(ItineraryService::class);
        return response($service->generateHtml($trip));
    }

    public function download(Trip $trip)
    {
        $service = app(ItineraryService::class);
        $pdf = $service->generatePdf($trip);

        return $pdf->download("itinerary-{$trip->trip_number}.pdf");
    }
}
