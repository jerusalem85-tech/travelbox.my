<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSchedule extends Model
{
    use SoftDeletes, HasUuid;

    public const TYPES = ['customer', 'supplier'];

    public const STATUSES = ['pending', 'partial', 'paid', 'overdue', 'cancelled'];

    protected $fillable = [
        'trip_id', 'customer_id', 'supplier_id', 'service_id', 'payment_id',
        'type', 'label', 'amount', 'currency', 'due_date', 'paid_date',
        'status', 'amount_paid', 'payment_method', 'reference', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'      => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'due_date'    => 'date',
            'paid_date'   => 'date',
        ];
    }

    // ─── Relationships ─────────────────────────────────────

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    // ─── Scopes ────────────────────────────────────────────

    public function scopeCustomer($query)
    {
        return $query->where('type', 'customer');
    }

    public function scopeSupplier($query)
    {
        return $query->where('type', 'supplier');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
                     ->orWhere(function ($q) {
                         $q->where('status', 'pending')
                           ->where('due_date', '<', now());
                     });
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ─── Accessors ─────────────────────────────────────────

    public function getRemainingAmountAttribute(): float
    {
        return $this->amount - $this->amount_paid;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'paid' && $this->due_date && $this->due_date->isPast();
    }

    public function getProgressPercentAttribute(): float
    {
        if ($this->amount > 0) {
            return ($this->amount_paid / $this->amount) * 100;
        }
        return 0;
    }

    // ─── Actions ───────────────────────────────────────────

    public function markPaid(float $amount = null, string $method = null, ?string $reference = null): void
    {
        $amount = $amount ?? $this->remaining_amount;

        $this->update([
            'amount_paid'     => $this->amount_paid + $amount,
            'paid_date'       => now(),
            'payment_method'  => $method ?? $this->payment_method,
            'reference'       => $reference ?? $this->reference,
            'status'          => ($this->amount_paid + $amount) >= $this->amount ? 'paid' : 'partial',
        ]);
    }

    // ─── Static Helpers ────────────────────────────────────

    public static function boot(): void
    {
        parent::boot();

        static::saving(function (PaymentSchedule $schedule) {
            if ($schedule->status === 'pending' && $schedule->due_date && $schedule->due_date->isPast()) {
                $schedule->status = 'overdue';
            }
        });
    }
}
