<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use SoftDeletes, HasUuid, HasFactory;

    protected $fillable = [
        'customer_code', 'type', 'first_name', 'last_name', 'company_name',
        'email', 'phone', 'mobile', 'address', 'city', 'country',
        'nationality', 'passport_number', 'passport_expiry',
        'passport_issue_date', 'passport_issue_place',
        'date_of_birth', 'place_of_birth', 'sex',
        'preferred_currency', 'notes', 'favorite_destinations',
        'loyalty_points', 'visa_info',
        'credit_limit', 'current_balance',
        'is_active', 'created_by',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(CustomerContact::class);
    }

    public function familyMembers(): HasMany
    {
        return $this->hasMany(CustomerFamilyMember::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payer');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'passport_expiry' => 'date',
            'passport_issue_date' => 'date',
            'visa_info' => 'array',
            'loyalty_points' => 'integer',
            'is_active' => 'boolean',
            'current_balance' => 'decimal:2',
            'credit_limit' => 'decimal:2',
            'favorite_destinations' => 'array',
        ];
    }

    public function recalculateBalance(): void
    {
        $totalInvoiced = (float) $this->invoices()->sum('total');
        $totalPaid = (float) $this->payments()
            ->whereIn('category', ['customer_payment', 'customer_deposit'])
            ->where('status', 'completed')
            ->sum('amount');
        $this->updateQuietly(['current_balance' => $totalInvoiced - $totalPaid]);
    }
}
