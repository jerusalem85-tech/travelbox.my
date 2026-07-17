<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelBooking extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'trip_id', 'supplier_id', 'hotel_name', 'city', 'address',
        'check_in', 'check_out', 'check_in_time', 'check_out_time',
        'room_type', 'meal_plan', 'number_of_rooms',
        'booking_reference', 'confirmation_number', 'status',
        'cost_price', 'selling_price', 'currency', 'notes',
        'cancellation_policy', 'latitude', 'longitude',
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'number_of_rooms' => 'integer',
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
