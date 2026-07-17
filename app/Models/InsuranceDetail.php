<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InsuranceDetail extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'policy_number', 'type', 'coverage_details',
        'start_date', 'end_date', 'provider',
        'max_coverage_amount', 'currency', 'exclusions',
    ];

    protected function casts(): array
    {
        return [
            'start_date'           => 'date',
            'end_date'             => 'date',
            'max_coverage_amount'  => 'decimal:2',
        ];
    }

    public function service(): HasOne
    {
        return $this->hasOne(Service::class, 'detail_id');
    }
}
