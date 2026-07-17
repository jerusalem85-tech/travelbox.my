<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CarDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'company', 'car_type', 'car_model',
        'pickup_location', 'dropoff_location',
        'pickup_datetime', 'dropoff_datetime',
        'booking_reference', 'license_plate',
        'include_insurance', 'included_km', 'daily_limit_km',
    ];

    protected function casts(): array
    {
        return [
            'pickup_datetime'     => 'datetime',
            'dropoff_datetime'    => 'datetime',
            'include_insurance'   => 'boolean',
            'included_km'         => 'integer',
            'daily_limit_km'      => 'integer',
        ];
    }

    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'detail_id');
    }
}
