<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HotelDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'hotel_name', 'city', 'address',
        'check_in', 'check_out',
        'check_in_time', 'check_out_time',
        'room_type', 'meal_plan', 'number_of_rooms',
        'booking_reference', 'confirmation_number',
        'cancellation_policy', 'latitude', 'longitude',
    ];

    protected function casts(): array
    {
        return [
            'check_in'        => 'date',
            'check_out'       => 'date',
            'latitude'        => 'decimal:7',
            'longitude'       => 'decimal:7',
            'number_of_rooms' => 'integer',
        ];
    }

    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'detail_id');
    }
}
