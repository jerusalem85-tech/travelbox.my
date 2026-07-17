<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\FlightSegment;
use App\Models\HotelBooking;
use App\Models\TransferBooking;
use App\Models\VisaApplication;
use App\Models\InsurancePolicy;
use App\Models\CruiseBooking;
use App\Models\TrainBooking;
use App\Models\CarRental;
use App\Models\PackageBooking;
use App\Models\OtherService;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    protected function configurePdf(\Barryvdh\DomPDF\PDF $pdf): \Barryvdh\DomPDF\PDF
    {
        $pdf->setPaper('a4');
        $pdf->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        return $pdf;
    }

    public function itinerary(Trip $trip): \Barryvdh\DomPDF\PDF
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

        $pdf = Pdf::loadView('pdfs.itinerary', compact('trip'));

        return $this->configurePdf($pdf);
    }

    public function invoice(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $invoice->load(['customer', 'items', 'trip']);

        $pdf = Pdf::loadView('pdfs.invoice', compact('invoice'));

        return $this->configurePdf($pdf);
    }

    public function receipt(Payment $payment): \Barryvdh\DomPDF\PDF
    {
        $payment->load(['trip', 'payer']);

        $pdf = Pdf::loadView('pdfs.receipt', compact('payment'));

        return $this->configurePdf($pdf);
    }

    public function voucher(Trip $trip): \Barryvdh\DomPDF\PDF
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

        $pdf = Pdf::loadView('pdfs.voucher', compact('trip'));

        return $this->configurePdf($pdf);
    }

    public function flightConfirmation(Trip $trip, FlightSegment $segment): \Barryvdh\DomPDF\PDF
    {
        $trip->load(['customer', 'passengers']);
        $segment->load(['supplier']);

        $pdf = Pdf::loadView('pdfs.flight-confirmation', compact('trip', 'segment'));

        return $this->configurePdf($pdf);
    }

    public function hotelVoucher(Trip $trip, HotelBooking $booking): \Barryvdh\DomPDF\PDF
    {
        $trip->load(['customer', 'passengers']);
        $booking->load(['supplier']);

        $pdf = Pdf::loadView('pdfs.hotel-voucher', compact('trip', 'booking'));

        return $this->configurePdf($pdf);
    }

    public function transferVoucher(Trip $trip, TransferBooking $booking): \Barryvdh\DomPDF\PDF
    {
        $trip->load(['customer', 'passengers']);

        $pdf = Pdf::loadView('pdfs.transfer-voucher', compact('trip', 'booking'));

        return $this->configurePdf($pdf);
    }

    public function visaConfirmation(Trip $trip, VisaApplication $visa): \Barryvdh\DomPDF\PDF
    {
        $trip->load(['customer', 'passengers']);

        $pdf = Pdf::loadView('pdfs.visa-confirmation', compact('trip', 'visa'));

        return $this->configurePdf($pdf);
    }

    public function insuranceCertificate(Trip $trip, InsurancePolicy $policy): \Barryvdh\DomPDF\PDF
    {
        $trip->load(['customer', 'passengers']);

        $pdf = Pdf::loadView('pdfs.insurance-certificate', compact('trip', 'policy'));

        return $this->configurePdf($pdf);
    }

    public function serviceSummary(Trip $trip): \Barryvdh\DomPDF\PDF
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

        $pdf = Pdf::loadView('pdfs.service-summary', compact('trip'));

        return $this->configurePdf($pdf);
    }

    public function cruiseVoucher(Trip $trip, CruiseBooking $booking): \Barryvdh\DomPDF\PDF
    {
        $trip->load(['customer', 'passengers']);
        $pdf = Pdf::loadView('pdfs.cruise-voucher', compact('trip', 'booking'));
        return $this->configurePdf($pdf);
    }

    public function trainVoucher(Trip $trip, TrainBooking $booking): \Barryvdh\DomPDF\PDF
    {
        $trip->load(['customer', 'passengers']);
        $pdf = Pdf::loadView('pdfs.train-voucher', compact('trip', 'booking'));
        return $this->configurePdf($pdf);
    }

    public function carRentalVoucher(Trip $trip, CarRental $booking): \Barryvdh\DomPDF\PDF
    {
        $trip->load(['customer', 'passengers']);
        $pdf = Pdf::loadView('pdfs.car-rental-voucher', compact('trip', 'booking'));
        return $this->configurePdf($pdf);
    }

    public function packageVoucher(Trip $trip, PackageBooking $booking): \Barryvdh\DomPDF\PDF
    {
        $trip->load(['customer', 'passengers']);
        $pdf = Pdf::loadView('pdfs.package-voucher', compact('trip', 'booking'));
        return $this->configurePdf($pdf);
    }

    public function otherServiceVoucher(Trip $trip, OtherService $booking): \Barryvdh\DomPDF\PDF
    {
        $trip->load(['customer', 'passengers']);
        $pdf = Pdf::loadView('pdfs.other-service-voucher', compact('trip', 'booking'));
        return $this->configurePdf($pdf);
    }
}
