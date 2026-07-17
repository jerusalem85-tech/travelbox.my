<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlightSegment extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'trip_id', 'supplier_id', 'type', 'airline', 'flight_number',
        'departure_airport', 'arrival_airport', 'departure_terminal', 'arrival_terminal',
        'departure_datetime', 'arrival_datetime',
        'booking_reference', 'ticket_number', 'class', 'cabin', 'fare_basis',
        'baggage', 'seat', 'meal',
        'status', 'cost_price', 'selling_price', 'currency', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'departure_datetime' => 'datetime',
            'arrival_datetime' => 'datetime',
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
