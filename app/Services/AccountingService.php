<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    private array $accountCache = [];

    public function postPayment(string $paymentId): void
    {
        $payment = \App\Models\Payment::with('trip')->findOrFail($paymentId);

        if ($payment->journalEntries()->exists()) return;

        $amount = (float) $payment->amount;
        $description = $payment->description ?: 'Payment ' . $payment->payment_number;

        if ($payment->category === 'customer_payment') {
            $this->createEntry(
                tripId: $payment->trip_id,
                date: $payment->payment_date,
                description: "Customer payment: {$description}",
                type: 'auto',
                reference: $payment,
                debitAccountCode: '1-001',
                creditAccountCode: '1-002',
                amount: $amount,
                createdBy: $payment->created_by,
            );
        } elseif ($payment->category === 'supplier_payment') {
            $this->createEntry(
                tripId: $payment->trip_id,
                date: $payment->payment_date,
                description: "Supplier payment: {$description}",
                type: 'auto',
                reference: $payment,
                debitAccountCode: '2-001',
                creditAccountCode: '1-001',
                amount: $amount,
                createdBy: $payment->created_by,
            );
        } elseif ($payment->category === 'customer_deposit') {
            $this->createEntry(
                tripId: $payment->trip_id,
                date: $payment->payment_date,
                description: "Customer deposit: {$description}",
                type: 'auto',
                reference: $payment,
                debitAccountCode: '1-001',
                creditAccountCode: '2-002',
                amount: $amount,
                createdBy: $payment->created_by,
            );
        } elseif ($payment->category === 'refund') {
            $this->createEntry(
                tripId: $payment->trip_id,
                date: $payment->payment_date,
                description: "Refund: {$description}",
                type: 'auto',
                reference: $payment,
                debitAccountCode: '1-002',
                creditAccountCode: '1-001',
                amount: $amount,
                createdBy: $payment->created_by,
            );
        }
    }

    public function postInvoice(string $invoiceId): void
    {
        $invoice = \App\Models\Invoice::with('trip', 'items')->findOrFail($invoiceId);

        if ($invoice->journalEntries()->exists()) return;

        $total = (float) $invoice->total;
        $tax = (float) $invoice->tax;
        $subtotal = (float) $invoice->subtotal;

        if ($invoice->type === 'sales') {
            $this->createEntry(
                tripId: $invoice->trip_id,
                date: $invoice->issue_date,
                description: "Invoice {$invoice->invoice_number} – {$invoice->customer?->full_name}",
                type: 'auto',
                reference: $invoice,
                debitAccountCode: '1-002',
                creditAccountCode: '4-007',
                amount: $total,
                createdBy: auth()->id(),
            );
        } elseif ($invoice->type === 'credit_note') {
            $this->createEntry(
                tripId: $invoice->trip_id,
                date: $invoice->issue_date,
                description: "Credit note {$invoice->invoice_number}",
                type: 'auto',
                reference: $invoice,
                debitAccountCode: '4-007',
                creditAccountCode: '1-002',
                amount: $total,
                createdBy: auth()->id(),
            );
        }
    }

    public function postServiceCost(string $serviceType, string $serviceId, string $tripId, float $cost, string $date, ?string $supplierName = null, ?string $description = null): void
    {
        $costMap = [
            'flight' => '5-001',
            'hotel' => '5-002',
            'transfer' => '5-003',
            'visa' => '5-004',
            'insurance' => '5-005',
            'activity' => '5-006',
        ];

        $costAccount = $costMap[$serviceType] ?? '5-009';
        $label = $description ?: ucfirst($serviceType) . " cost";

        $modelClass = match ($serviceType) {
            'flight' => \App\Models\FlightSegment::class,
            'hotel' => \App\Models\HotelBooking::class,
            'transfer' => \App\Models\TransferBooking::class,
            'visa' => \App\Models\VisaApplication::class,
            'insurance' => \App\Models\InsurancePolicy::class,
            'activity' => \App\Models\Activity::class,
            default => null,
        };

        $reference = $modelClass ? $modelClass::find($serviceId) : null;

        $this->createEntry(
            tripId: $tripId,
            date: $date,
            description: "{$label}" . ($supplierName ? " – {$supplierName}" : ''),
            type: 'auto',
            reference: $reference,
            debitAccountCode: $costAccount,
            creditAccountCode: '2-001',
            amount: $cost,
            createdBy: auth()->id(),
        );
    }

    public function postExpense(string $expenseId): void
    {
        $expense = \App\Models\TripExpense::with('trip')->findOrFail($expenseId);

        if ($expense->journalEntries()->exists()) return;

        $this->createEntry(
            tripId: $expense->trip_id,
            date: $expense->expense_date,
            description: "Expense: {$expense->description}",
            type: 'auto',
            reference: $expense,
            debitAccountCode: '5-009',
            creditAccountCode: '1-001',
            amount: (float) $expense->amount,
            createdBy: auth()->id(),
        );
    }

    public function reverseEntry(string $referenceType, string $referenceId): void
    {
        JournalEntry::where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->delete();
    }

    private function createEntry(string $tripId, string $date, string $description, string $type, $reference, string $debitAccountCode, string $creditAccountCode, float $amount, ?string $createdBy): JournalEntry
    {
        if ($amount <= 0) return null;

        return DB::transaction(function () use ($tripId, $date, $description, $type, $reference, $debitAccountCode, $creditAccountCode, $amount, $createdBy) {
            $entry = JournalEntry::create([
                'entry_number' => $this->nextEntryNumber(),
                'trip_id' => $tripId ?: null,
                'date' => $date,
                'description' => $description,
                'type' => $type,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
                'created_by' => $createdBy ?? auth()->id(),
            ]);

            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $this->getAccountId($debitAccountCode),
                'debit' => $amount,
                'credit' => 0,
                'description' => $description,
            ]);

            JournalEntryItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $this->getAccountId($creditAccountCode),
                'debit' => 0,
                'credit' => $amount,
                'description' => $description,
            ]);

            return $entry;
        });
    }

    private function getAccountId(string $code): string
    {
        if (!isset($this->accountCache[$code])) {
            $this->accountCache[$code] = ChartOfAccount::where('code', $code)->value('id');
        }
        return $this->accountCache[$code];
    }

    private function nextEntryNumber(): string
    {
        $year = now()->format('Y');
        $last = JournalEntry::where('entry_number', 'like', "JE-{$year}-%")
            ->orderBy('entry_number', 'desc')->first();
        $num = $last ? (int) substr($last->entry_number, 8) + 1 : 1;
        return 'JE-' . $year . '-' . str_pad($num, 5, '0', STR_PAD_LEFT);
    }
}
