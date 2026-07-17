<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Passenger extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'trip_id', 'customer_id', 'first_name', 'last_name',
        'date_of_birth', 'nationality', 'passport_number',
        'passport_expiry', 'passport_issue_date', 'passport_issue_place',
        'meal_preference', 'seat_preference', 'ffp_number', 'ffp_airline',
        'special_requests',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'passport_expiry' => 'date',
            'passport_issue_date' => 'date',
            'special_requests' => 'array',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getBenefitsSummaryAttribute(): string
    {
        $parts = [];
        if ($this->meal_preference) $parts[] = $this->meal_preference;
        if ($this->seat_preference) $parts[] = $this->seat_preference;
        if ($this->ffp_number) $parts[] = $this->ffp_airline ? "{$this->ffp_airline} {$this->ffp_number}" : $this->ffp_number;
        return implode(' · ', $parts);
    }
}
