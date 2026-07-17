<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TransferDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'type', 'pickup_location', 'dropoff_location',
        'pickup_datetime', 'vehicle_type',
        'number_of_passengers', 'booking_reference',
        'driver_name', 'driver_phone', 'vehicle_plate',
    ];

    protected function casts(): array
    {
        return [
            'pickup_datetime'       => 'datetime',
            'number_of_passengers'  => 'integer',
        ];
    }

    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'detail_id');
    }
}
