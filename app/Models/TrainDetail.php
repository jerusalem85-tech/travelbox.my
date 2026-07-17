<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TrainDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'company', 'train_number',
        'departure_station', 'arrival_station',
        'departure_datetime', 'arrival_datetime',
        'class', 'carriage', 'seat',
        'booking_reference', 'ticket_type',
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
