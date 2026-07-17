<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\TripAutomationLog;
use App\Services\AccountingService;
use App\Services\EmailService;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TripAutomationService
{
    public function run(Trip $trip): array
    {
        $results = [];

        $results[] = $this->createInvoice($trip);
        $results[] = $this->postAccounting($trip);
        $results[] = $this->updateCustomerBalance($trip);
        $results[] = $this->sendEmail($trip);
        $results[] = $this->sendWhatsApp($trip);

        return $results;
    }

    public function createInvoice(Trip $trip): TripAutomationLog
    {
        try {
            if ($trip->invoices()->exists()) {
                return $this->log($trip, 'invoice_created', 'skipped', 'Invoice already exists');
            }

            $invoice = DB::transaction(function () use ($trip) {
                $last = Invoice::where('invoice_number', 'like', 'INV-' . date('Y') . '-%')
                    ->orderBy('invoice_number', 'desc')->first();
                $num = $last ? (int) substr($last->invoice_number, 9) + 1 : 1;
                $number = 'INV-' . date('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);

                $invoice = Invoice::create([
                    'invoice_number' => $number,
                    'trip_id' => $trip->id,
                    'customer_id' => $trip->customer_id,
                    'type' => 'sales',
                    'issue_date' => now(),
                    'due_date' => now()->addDays(30),
                    'subtotal' => $trip->total_selling_price,
                    'tax' => 0,
                    'total' => $trip->total_selling_price,
                    'status' => 'unpaid',
                    'notes' => 'Auto-generated invoice for trip ' . $trip->trip_number,
                ]);

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $trip->name ?: 'Trip package - ' . $trip->destination,
                    'service_type' => 'trip_package',
                    'quantity' => 1,
                    'unit_price' => $trip->total_selling_price,
                    'total' => $trip->total_selling_price,
                ]);

                return $invoice;
            });

            $trip->logTimeline('invoice_created', "Invoice {$invoice->invoice_number} auto-generated");
            return $this->log($trip, 'invoice_created', 'success', "Invoice {$invoice->invoice_number} created");
        } catch (\Exception $e) {
            Log::error('Auto invoice failed: ' . $e->getMessage());
            return $this->log($trip, 'invoice_created', 'failed', $e->getMessage());
        }
    }

    public function postAccounting(Trip $trip): TripAutomationLog
    {
        try {
            $service = app(AccountingService::class);

            foreach ($trip->flightSegments as $fs) {
                if ($fs->cost_price > 0) {
                    $service->postServiceCost('flight', $fs->id, $trip->id, (float) $fs->cost_price, now()->toDateString(), $fs->supplier?->name);
                }
            }
            foreach ($trip->hotelBookings as $hb) {
                if ($hb->cost_price > 0) {
                    $service->postServiceCost('hotel', $hb->id, $trip->id, (float) $hb->cost_price, now()->toDateString(), $hb->supplier?->name);
                }
            }
            foreach ($trip->transferBookings as $tb) {
                if ($tb->cost_price > 0) {
                    $service->postServiceCost('transfer', $tb->id, $trip->id, (float) $tb->cost_price, now()->toDateString(), $tb->supplier?->name);
                }
            }
            foreach ($trip->visaApplications as $v) {
                if ($v->cost_price > 0) {
                    $service->postServiceCost('visa', $v->id, $trip->id, (float) $v->cost_price, now()->toDateString());
                }
            }
            foreach ($trip->insurancePolicies as $ins) {
                if ($ins->cost_price > 0) {
                    $service->postServiceCost('insurance', $ins->id, $trip->id, (float) $ins->cost_price, now()->toDateString());
                }
            }
            foreach ($trip->activities as $act) {
                if ($act->cost_price > 0) {
                    $service->postServiceCost('activity', $act->id, $trip->id, (float) $act->cost_price, now()->toDateString());
                }
            }

            $trip->logTimeline('accounting_posted', 'Service costs posted to general ledger');
            return $this->log($trip, 'accounting_posted', 'success', 'All service costs posted');
        } catch (\Exception $e) {
            Log::error('Auto accounting failed: ' . $e->getMessage());
            return $this->log($trip, 'accounting_posted', 'failed', $e->getMessage());
        }
    }

    public function updateCustomerBalance(Trip $trip): TripAutomationLog
    {
        try {
            if ($trip->customer) {
                $trip->customer->recalculateBalance();
                $trip->logTimeline('balance_updated', 'Customer balance recalculated');
                return $this->log($trip, 'balance_updated', 'success', 'Customer balance: ' . number_format($trip->customer->current_balance, 2));
            }
            return $this->log($trip, 'balance_updated', 'skipped', 'No customer');
        } catch (\Exception $e) {
            return $this->log($trip, 'balance_updated', 'failed', $e->getMessage());
        }
    }

    public function sendEmail(Trip $trip): TripAutomationLog
    {
        try {
            if (!$trip->customer?->email) {
                return $this->log($trip, 'email_sent', 'skipped', 'No customer email');
            }

            $service = app(EmailService::class);
            $service->sendTripDetail($trip, $trip->customer->email, 'Your trip booking has been confirmed!');
            $trip->logTimeline('email_sent', "Confirmation email sent to {$trip->customer->email}");
            return $this->log($trip, 'email_sent', 'success', "Email sent to {$trip->customer->email}");
        } catch (\Exception $e) {
            Log::error('Auto email failed: ' . $e->getMessage());
            return $this->log($trip, 'email_sent', 'failed', $e->getMessage());
        }
    }

    public function sendWhatsApp(Trip $trip): TripAutomationLog
    {
        try {
            $phone = $trip->customer?->mobile ?? $trip->customer?->phone;
            if (!$phone) {
                return $this->log($trip, 'whatsapp_sent', 'skipped', 'No customer phone');
            }

            $service = app(WhatsAppService::class);
            $service->sendTripDetail($trip, $phone);
            $trip->logTimeline('whatsapp_sent', "Trip details sent via WhatsApp to {$phone}");
            return $this->log($trip, 'whatsapp_sent', 'success', "WhatsApp sent to {$phone}");
        } catch (\Exception $e) {
            Log::error('Auto WhatsApp failed: ' . $e->getMessage());
            return $this->log($trip, 'whatsapp_sent', 'failed', $e->getMessage());
        }
    }

    private function log(Trip $trip, string $action, string $status, ?string $result = null): TripAutomationLog
    {
        return TripAutomationLog::create([
            'trip_id' => $trip->id,
            'action' => $action,
            'status' => $status,
            'result' => $result,
        ]);
    }
}
