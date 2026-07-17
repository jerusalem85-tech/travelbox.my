<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ActivityDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'type', 'location', 'date', 'time',
        'duration', 'number_of_participants',
        'booking_reference', 'guide_name', 'guide_language',
        'inclusions', 'exclusions',
    ];

    protected function casts(): array
    {
        return [
            'date'                  => 'date',
            'time'                  => 'datetime',
            'duration'              => 'integer',
            'number_of_participants' => 'integer',
        ];
    }

    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'detail_id');
    }
}
