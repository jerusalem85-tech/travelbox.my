<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FlightDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'airline', 'flight_number',
        'departure_airport', 'arrival_airport',
        'departure_terminal', 'arrival_terminal',
        'departure_datetime', 'arrival_datetime',
        'booking_reference', 'ticket_number',
        'class', 'cabin', 'fare_basis',
        'baggage', 'seat', 'meal',
    ];

    protected function casts(): array
    {
        return [
            'departure_datetime' => 'datetime',
            'arrival_datetime'   => 'datetime',
        ];
    }

    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'detail_id');
    }
}
