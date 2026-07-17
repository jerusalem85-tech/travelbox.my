<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TripController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PdfController;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('dashboard', \App\Livewire\AdminDashboard::class)->name('dashboard');

    Route::view('profile', 'profile')->name('profile');

    Route::get('trips', \App\Livewire\Trips\TripList::class)->name('trips.index');
    Route::get('trips/create', \App\Livewire\Trips\TripForm::class)->name('trips.create');
    Route::get('trips/calendar', \App\Livewire\Trips\TripCalendar::class)->name('trips.calendar');
    Route::get('trips/pipeline', \App\Livewire\Trips\TripPipeline::class)->name('trips.pipeline');

    Route::get('trips/{trip}/edit', \App\Livewire\Trips\TripForm::class)->name('trips.edit');
    Route::get('trips/{trip}/itinerary', [\App\Http\Controllers\ItineraryController::class, 'view'])->name('trips.itinerary.view');
    Route::get('trips/{trip}/itinerary/pdf', [\App\Http\Controllers\ItineraryController::class, 'download'])->name('trips.itinerary.download');
    Route::get('trips/{trip}/flights', \App\Livewire\Trips\FlightReservation::class)->name('trips.flights');
    Route::get('trips/{trip}/hotels', \App\Livewire\Trips\HotelReservation::class)->name('trips.hotels');
    Route::get('trips/{trip}', \App\Livewire\Trips\TripShow::class)->name('trips.show');

    Route::prefix('pdf')->name('pdfs.')->group(function () {
        Route::get('trips/{trip}/itinerary', [PdfController::class, 'itinerary'])->name('itinerary');
        Route::get('trips/{trip}/voucher', [PdfController::class, 'voucher'])->name('voucher');
        Route::get('trips/{trip}/service-summary', [PdfController::class, 'serviceSummary'])->name('service-summary');
        Route::get('trips/{trip}/flights/{segment}/confirmation', [PdfController::class, 'flightConfirmation'])->name('flight-confirmation');
        Route::get('trips/{trip}/hotels/{booking}/voucher', [PdfController::class, 'hotelVoucher'])->name('hotel-voucher');
        Route::get('trips/{trip}/transfers/{booking}/voucher', [PdfController::class, 'transferVoucher'])->name('transfer-voucher');
        Route::get('trips/{trip}/visas/{visa}/confirmation', [PdfController::class, 'visaConfirmation'])->name('visa-confirmation');
        Route::get('trips/{trip}/insurance/{policy}/certificate', [PdfController::class, 'insuranceCertificate'])->name('insurance-certificate');
        Route::get('trips/{trip}/cruises/{booking}/voucher', [PdfController::class, 'cruiseVoucher'])->name('cruise-voucher');
        Route::get('trips/{trip}/trains/{booking}/voucher', [PdfController::class, 'trainVoucher'])->name('train-voucher');
        Route::get('trips/{trip}/cars/{booking}/voucher', [PdfController::class, 'carRentalVoucher'])->name('car-rental-voucher');
        Route::get('trips/{trip}/packages/{booking}/voucher', [PdfController::class, 'packageVoucher'])->name('package-voucher');
        Route::get('trips/{trip}/others/{booking}/voucher', [PdfController::class, 'otherServiceVoucher'])->name('other-service-voucher');
        Route::get('invoices/{invoice}/pdf', [PdfController::class, 'invoice'])->name('invoice');
        Route::get('payments/{payment}/receipt', [PdfController::class, 'receipt'])->name('receipt');
    });

    Route::get('customers', \App\Livewire\Customers\CustomerList::class)->name('customers.index');
    Route::get('customers/create', \App\Livewire\Customers\CustomerForm::class)->name('customers.create');
    Route::get('customers/{customer}/edit', \App\Livewire\Customers\CustomerForm::class)->name('customers.edit');
    Route::get('customers/{customer}', \App\Livewire\Customers\CustomerShow::class)->name('customers.show');

    Route::get('suppliers', \App\Livewire\Suppliers\SupplierList::class)->name('suppliers.index');
    Route::get('suppliers/create', \App\Livewire\Suppliers\SupplierForm::class)->name('suppliers.create');
    Route::get('suppliers/{supplier}/edit', \App\Livewire\Suppliers\SupplierForm::class)->name('suppliers.edit');
    Route::get('suppliers/{supplier}', \App\Livewire\Suppliers\SupplierShow::class)->name('suppliers.show');

    Route::get('invoices', \App\Livewire\Invoices\InvoiceList::class)->name('invoices.index');
    Route::get('invoices/create', \App\Livewire\Invoices\InvoiceForm::class)->name('invoices.create');
    Route::get('invoices/{invoice}/edit', \App\Livewire\Invoices\InvoiceForm::class)->name('invoices.edit');
    Route::get('invoices/{invoice}', \App\Livewire\Invoices\InvoiceShow::class)->name('invoices.show');

    Route::get('payments', \App\Livewire\Payments\PaymentList::class)->name('payments.index');
    Route::get('payments/create', \App\Livewire\Payments\PaymentForm::class)->name('payments.create');
    Route::get('payments/{payment}/edit', \App\Livewire\Payments\PaymentForm::class)->name('payments.edit');

    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::view('chart-of-accounts', 'accounting.chart-of-accounts')->name('chart-of-accounts');
        Route::get('general-ledger', \App\Livewire\Accounting\GeneralLedger::class)->name('general-ledger');
        Route::get('trial-balance', \App\Livewire\Accounting\TrialBalance::class)->name('trial-balance');
        Route::get('profit-loss', \App\Livewire\Accounting\ProfitLoss::class)->name('profit-loss');
        Route::get('balance-sheet', \App\Livewire\Accounting\BalanceSheet::class)->name('balance-sheet');
        Route::get('cash-flow', \App\Livewire\Accounting\CashFlow::class)->name('cash-flow');
    });

    Route::get('expenses', \App\Livewire\Expenses\ExpenseList::class)->name('expenses.index');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('sales', \App\Livewire\Reports\SalesReport::class)->name('sales');
        Route::get('profit', \App\Livewire\Reports\ProfitReport::class)->name('profit');
        Route::get('commission', \App\Livewire\Reports\CommissionReport::class)->name('commission');
        Route::get('customer-aging', \App\Livewire\Reports\CustomerAgingReport::class)->name('customer-aging');
        Route::get('supplier-aging', \App\Livewire\Reports\SupplierAgingReport::class)->name('supplier-aging');
        Route::get('tax-summary', \App\Livewire\Reports\TaxSummaryReport::class)->name('tax-summary');
    });

    Route::view('settings', 'settings.index')->name('settings.index');

});

require __DIR__.'/auth.php';
