<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OtherDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name', 'category', 'description',
        'service_date', 'location',
        'booking_reference', 'quantity',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'quantity'     => 'integer',
        ];
    }

    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'detail_id');
    }
}
