<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use App\Models\Trip;
use App\Models\Invoice;
use App\Services\AccountingService;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PaymentForm extends Component
{
    public ?Payment $payment = null;
    public bool $editing = false;

    public string $trip_id = '';
    public string $invoice_id = '';
    public string $category = 'customer_payment';
    public string $payment_method = 'bank_transfer';
    public string $amount = '0';
    public string $currency = 'USD';
    public string $exchange_rate = '1';
    public string $payment_date = '';
    public string $reference = '';
    public string $description = '';
    public string $status = 'completed';

    public function mount(?Payment $payment = null): void
    {
        $this->payment = $payment;
        if ($payment) {
            $this->editing = true;
            $this->trip_id = $payment->trip_id ?? '';
            $this->invoice_id = $payment->invoice_id ?? '';
            $this->category = $payment->category;
            $this->payment_method = $payment->payment_method;
            $this->amount = (string) $payment->amount;
            $this->currency = $payment->currency;
            $this->exchange_rate = (string) ($payment->exchange_rate ?? 1);
            $this->payment_date = $payment->payment_date?->format('Y-m-d') ?? '';
            $this->reference = $payment->reference ?? '';
            $this->description = $payment->description ?? '';
            $this->status = $payment->status;
        } else {
            $this->payment_date = now()->format('Y-m-d');
        }
    }

    public function save(): void
    {
        $this->validate([
            'category' => 'required|string',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'payment_date' => 'required|date',
            'status' => 'required|string',
        ]);

        $data = [
            'trip_id' => $this->trip_id ?: null,
            'invoice_id' => $this->invoice_id ?: null,
            'category' => $this->category,
            'payment_method' => $this->payment_method,
            'amount' => (float) $this->amount,
            'currency' => $this->currency,
            'exchange_rate' => (float) $this->exchange_rate,
            'payment_date' => $this->payment_date,
            'reference' => $this->reference ?: null,
            'description' => $this->description ?: null,
            'status' => $this->status,
            'created_by' => auth()->id() ?? 1,
        ];

        $accounting = app(AccountingService::class);

        if ($this->editing && $this->payment) {
            $this->payment->update($data);
            $accounting->reverseEntry(\App\Models\Payment::class, $this->payment->id);
            $accounting->postPayment($this->payment->id);
            $this->dispatch('notify', type: 'success', title: 'Payment Updated', message: 'Payment updated successfully.');
        } else {
            $last = Payment::where('payment_number', 'like', 'PAY-' . now()->format('Y') . '-%')
                ->orderBy('payment_number', 'desc')->first();
            $num = $last ? (int) substr($last->payment_number, 9) + 1 : 1;
            $data['payment_number'] = 'PAY-' . now()->format('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
            $payment = Payment::create($data);
            $accounting->postPayment($payment->id);
            if ($payment->trip) {
                app(\App\Services\NotificationService::class)->paymentReceived($payment->trip, $payment->amount, $payment->currency);
            }
            $this->dispatch('notify', type: 'success', title: 'Payment Recorded', message: 'Payment recorded successfully.');
        }

        $this->redirect(route('payments.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.payments.payment-form', [
            'trips' => Trip::orderBy('created_at', 'desc')->get(),
            'invoices' => Invoice::where('status', 'pending')->orWhere('status', 'overdue')->orderBy('issue_date', 'desc')->get(),
        ]);
    }
}
