<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CruiseBooking extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'trip_id', 'supplier_id', 'cruise_line', 'ship_name',
        'cabin_type', 'cabin_number', 'departure_port', 'arrival_port',
        'departure_date', 'arrival_date', 'itinerary',
        'booking_reference', 'status',
        'cost_price', 'selling_price', 'currency', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'arrival_date' => 'date',
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
