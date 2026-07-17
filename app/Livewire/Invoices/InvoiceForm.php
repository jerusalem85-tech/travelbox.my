<?php

namespace App\Livewire\Invoices;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Trip;
use App\Services\AccountingService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class InvoiceForm extends Component
{
    public ?Invoice $invoice = null;
    public bool $editing = false;

    public string $customer_id = '';
    public string $trip_id = '';
    public string $type = 'sales';
    public string $issue_date = '';
    public string $due_date = '';
    public string $status = 'draft';
    public string $notes = '';

    public array $items = [];

    protected function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'trip_id' => 'nullable|exists:trips,id',
            'type' => 'required|in:sales,purchase,credit_note,debit_note',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function mount(?Invoice $invoice = null): void
    {
        $this->invoice = $invoice;
        if ($invoice) {
            $this->editing = true;
            $this->customer_id = $invoice->customer_id;
            $this->trip_id = $invoice->trip_id ?? '';
            $this->type = $invoice->type;
            $this->issue_date = $invoice->issue_date?->format('Y-m-d') ?? now()->format('Y-m-d');
            $this->due_date = $invoice->due_date?->format('Y-m-d') ?? now()->addDays(30)->format('Y-m-d');
            $this->status = $invoice->status;
            $this->notes = $invoice->notes ?? '';
            foreach ($invoice->items as $item) {
                $this->items[] = [
                    'id' => $item->id,
                    'description' => $item->description,
                    'quantity' => (string) $item->quantity,
                    'unit_price' => (string) $item->unit_price,
                ];
            }
        } else {
            $this->issue_date = now()->format('Y-m-d');
            $this->due_date = now()->addDays(30)->format('Y-m-d');
            $this->addItem();
        }
    }

    public function addItem(): void
    {
        $this->items[] = ['description' => '', 'quantity' => '1', 'unit_price' => '0'];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save(): void
    {
        $this->validate();

        $subtotal = 0;
        foreach ($this->items as $item) {
            $subtotal += (float) $item['unit_price'] * (int) $item['quantity'];
        }
        $tax = 0;
        $total = $subtotal + $tax;

        DB::transaction(function () use ($subtotal, $tax, $total) {
            $data = [
                'customer_id' => $this->customer_id,
                'trip_id' => $this->trip_id ?: null,
                'type' => $this->type,
                'issue_date' => $this->issue_date,
                'due_date' => $this->due_date,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => $this->status,
                'notes' => $this->notes ?: null,
            ];

            $accounting = app(AccountingService::class);

            if ($this->editing && $this->invoice) {
                $this->invoice->update($data);
                $this->invoice->items()->delete();
                $invoiceId = $this->invoice->id;
            } else {
                $last = Invoice::where('invoice_number', 'like', 'INV-' . now()->format('Y') . '-%')
                    ->orderBy('invoice_number', 'desc')
                    ->lockForUpdate()
                    ->first();
                $num = $last ? (int) substr($last->invoice_number, 9) + 1 : 1;
                $data['invoice_number'] = 'INV-' . now()->format('Y') . '-' . str_pad($num, 4, '0', STR_PAD_LEFT);
                $invoice = Invoice::create($data);
                $invoiceId = $invoice->id;
            }

            foreach ($this->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoiceId,
                    'description' => $item['description'],
                    'quantity' => (int) $item['quantity'],
                    'unit_price' => (float) $item['unit_price'],
                    'total' => (float) $item['unit_price'] * (int) $item['quantity'],
                ]);
            }

            $accounting->reverseEntry(\App\Models\Invoice::class, $invoiceId);
            $accounting->postInvoice($invoiceId);

            if (!$this->editing && isset($invoice) && $invoice->trip) {
                app(NotificationService::class)->invoiceCreated($invoice->trip, $invoice->invoice_number);
            }
        });

        $this->dispatch('notify', type: 'success', title: $this->editing ? 'Invoice Updated' : 'Invoice Created', message: ($this->editing ? 'Invoice updated' : 'Invoice created') . ' successfully.');
        $this->redirect(route('invoices.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.invoices.invoice-form', [
            'customers' => Customer::where('is_active', true)->orderBy('first_name')->get(),
            'trips' => Trip::orderBy('created_at', 'desc')->get(),
        ]);
    }
}
