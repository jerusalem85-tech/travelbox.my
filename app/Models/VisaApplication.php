<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaApplication extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'trip_id', 'passenger_id', 'supplier_id', 'country',
        'visa_type', 'application_date', 'expected_delivery_date',
        'actual_delivery_date', 'status', 'cost_price', 'selling_price',
        'currency', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'application_date' => 'date',
            'expected_delivery_date' => 'date',
            'actual_delivery_date' => 'date',
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function passenger(): BelongsTo
    {
        return $this->belongsTo(Passenger::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
