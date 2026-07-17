<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VisaDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'country', 'visa_type', 'application_date',
        'expected_delivery_date', 'actual_delivery_date',
        'embassy', 'visa_number', 'number_of_entries',
        'validity_days', 'requirements',
    ];

    protected function casts(): array
    {
        return [
            'application_date'       => 'date',
            'expected_delivery_date' => 'date',
            'actual_delivery_date'   => 'date',
            'number_of_entries'      => 'integer',
            'validity_days'          => 'integer',
        ];
    }

    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'detail_id');
    }
}
