<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PackageDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'type', 'description',
        'start_date', 'end_date', 'destination',
        'number_of_nights', 'number_of_rooms',
        'room_type', 'meal_plan', 'booking_reference',
        'inclusions', 'exclusions',
    ];

    protected function casts(): array
    {
        return [
            'start_date'       => 'date',
            'end_date'         => 'date',
            'number_of_nights' => 'integer',
            'number_of_rooms'  => 'integer',
        ];
    }

    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'detail_id');
    }
}
