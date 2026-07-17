<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CruiseDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'cruise_line', 'ship_name', 'cabin_type', 'cabin_number',
        'departure_port', 'arrival_port',
        'departure_date', 'arrival_date', 'itinerary',
        'booking_reference', 'deck', 'meal_plan',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'arrival_date'   => 'date',
        ];
    }

    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'detail_id');
    }
}
