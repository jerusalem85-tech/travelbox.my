<?php

namespace App\Http\Controllers;

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
use App\Services\PdfService;

class PdfController extends Controller
{
    public function __construct(
        protected PdfService $pdfService,
    ) {}

    public function itinerary(Trip $trip)
    {
        return $this->pdfService->itinerary($trip)
            ->download("itinerary-{$trip->trip_number}.pdf");
    }

    public function invoice(Invoice $invoice)
    {
        return $this->pdfService->invoice($invoice)
            ->download("invoice-{$invoice->invoice_number}.pdf");
    }

    public function receipt(Payment $payment)
    {
        return $this->pdfService->receipt($payment)
            ->download("receipt-{$payment->payment_number}.pdf");
    }

    public function voucher(Trip $trip)
    {
        return $this->pdfService->voucher($trip)
            ->download("voucher-{$trip->trip_number}.pdf");
    }

    public function flightConfirmation(Trip $trip, FlightSegment $segment)
    {
        return $this->pdfService->flightConfirmation($trip, $segment)
            ->download("flight-confirmation-{$segment->id}.pdf");
    }

    public function hotelVoucher(Trip $trip, HotelBooking $booking)
    {
        return $this->pdfService->hotelVoucher($trip, $booking)
            ->download("hotel-voucher-{$booking->id}.pdf");
    }

    public function transferVoucher(Trip $trip, TransferBooking $booking)
    {
        return $this->pdfService->transferVoucher($trip, $booking)
            ->download("transfer-voucher-{$booking->id}.pdf");
    }

    public function visaConfirmation(Trip $trip, VisaApplication $visa)
    {
        return $this->pdfService->visaConfirmation($trip, $visa)
            ->download("visa-confirmation-{$visa->id}.pdf");
    }

    public function insuranceCertificate(Trip $trip, InsurancePolicy $policy)
    {
        return $this->pdfService->insuranceCertificate($trip, $policy)
            ->download("insurance-certificate-{$policy->id}.pdf");
    }

    public function serviceSummary(Trip $trip)
    {
        return $this->pdfService->serviceSummary($trip)
            ->download("services-{$trip->trip_number}.pdf");
    }

    public function cruiseVoucher(Trip $trip, CruiseBooking $booking)
    {
        return $this->pdfService->cruiseVoucher($trip, $booking)
            ->download("cruise-voucher-{$trip->trip_number}.pdf");
    }

    public function trainVoucher(Trip $trip, TrainBooking $booking)
    {
        return $this->pdfService->trainVoucher($trip, $booking)
            ->download("train-voucher-{$trip->trip_number}.pdf");
    }

    public function carRentalVoucher(Trip $trip, CarRental $booking)
    {
        return $this->pdfService->carRentalVoucher($trip, $booking)
            ->download("car-rental-voucher-{$trip->trip_number}.pdf");
    }

    public function packageVoucher(Trip $trip, PackageBooking $booking)
    {
        return $this->pdfService->packageVoucher($trip, $booking)
            ->download("package-voucher-{$trip->trip_number}.pdf");
    }

    public function otherServiceVoucher(Trip $trip, OtherService $booking)
    {
        return $this->pdfService->otherServiceVoucher($trip, $booking)
            ->download("other-service-voucher-{$trip->trip_number}.pdf");
    }
}
