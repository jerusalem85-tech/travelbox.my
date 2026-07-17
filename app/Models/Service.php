<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\MorphTo;

class Service extends Model
{
    use SoftDeletes, HasUuid;

    public const TYPES = [
        'flight'    => 'Flight',
        'hotel'     => 'Hotel',
        'transfer'  => 'Transfer',
        'visa'      => 'Visa',
        'insurance' => 'Insurance',
        'activity'  => 'Activity',
        'cruise'    => 'Cruise',
        'train'     => 'Train',
        'car'       => 'Car Rental',
        'package'   => 'Package',
        'other'     => 'Other',
    ];

    public const STATUSES = ['pending', 'confirmed', 'cancelled', 'completed'];

    protected $fillable = [
        'trip_id', 'supplier_id', 'passenger_id', 'type', 'name',
        'status', 'cost_price', 'selling_price', 'currency', 'notes',
        'supplier_booking_reference', 'supplier_cost', 'supplier_currency',
        'supplier_due_date', 'supplier_status', 'confirmation_file',
        'detail_type', 'detail_id',
        'service_date', 'service_end_date',
    ];

    protected function casts(): array
    {
        return [
            'cost_price'       => 'decimal:2',
            'selling_price'    => 'decimal:2',
            'supplier_cost'    => 'decimal:2',
            'supplier_due_date' => 'date',
            'service_date'     => 'date',
            'service_end_date' => 'date',
        ];
    }

    // ─── Relationships ─────────────────────────────────────

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(Passenger::class);
    }

    public function detail(): MorphTo
    {
        return $this->morphTo();
    }

    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    // ─── Scope Shortcuts ───────────────────────────────────

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForTrip($query, $tripId)
    {
        return $query->where('trip_id', $tripId);
    }

    // ─── Accessors ─────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getMarginAttribute(): float
    {
        return $this->selling_price - $this->cost_price;
    }

    public function getMarginPercentAttribute(): float
    {
        if ($this->cost_price > 0) {
            return ($this->margin / $this->cost_price) * 100;
        }
        return 0;
    }

    public function getIsPaidAttribute(): bool
    {
        return $this->supplier_status === 'paid' || $this->supplier_status === 'confirmed';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->supplier_due_date && $this->supplier_due_date->isPast() && !$this->is_paid;
    }

    public function getDisplayIconAttribute(): string
    {
        return match($this->type) {
            'flight'    => 'fa-plane',
            'hotel'     => 'fa-hotel',
            'transfer'  => 'fa-shuttle-van',
            'visa'      => 'fa-passport',
            'insurance' => 'fa-shield-alt',
            'activity'  => 'fa-ticket-alt',
            'cruise'    => 'fa-ship',
            'train'     => 'fa-train',
            'car'       => 'fa-car',
            'package'   => 'fa-box',
            default     => 'fa-cog',
        };
    }

    public function getDisplayColorAttribute(): string
    {
        return match($this->type) {
            'flight'    => 'indigo',
            'hotel'     => 'emerald',
            'transfer'  => 'orange',
            'visa'      => 'purple',
            'insurance' => 'pink',
            'activity'  => 'cyan',
            'cruise'    => 'sky',
            'train'     => 'stone',
            'car'       => 'yellow',
            'package'   => 'teal',
            default     => 'gray',
        };
    }
}
