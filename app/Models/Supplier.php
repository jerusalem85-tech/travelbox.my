<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplier extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'supplier_code', 'type', 'company_name', 'contact_person',
        'email', 'phone', 'mobile', 'address', 'city', 'country',
        'preferred_currency', 'payment_terms', 'contract_notes',
        'current_balance', 'is_active', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'current_balance' => 'decimal:2',
        ];
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(SupplierContact::class);
    }

    public function flightSegments(): HasMany
    {
        return $this->hasMany(FlightSegment::class);
    }

    public function hotelBookings(): HasMany
    {
        return $this->hasMany(HotelBooking::class);
    }

    public function transferBookings(): HasMany
    {
        return $this->hasMany(TransferBooking::class);
    }

    public function visaApplications(): HasMany
    {
        return $this->hasMany(VisaApplication::class);
    }

    public function insurancePolicies(): HasMany
    {
        return $this->hasMany(InsurancePolicy::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function cruiseBookings(): HasMany
    {
        return $this->hasMany(CruiseBooking::class);
    }

    public function trainBookings(): HasMany
    {
        return $this->hasMany(TrainBooking::class);
    }

    public function carRentals(): HasMany
    {
        return $this->hasMany(CarRental::class);
    }

    public function packageBookings(): HasMany
    {
        return $this->hasMany(PackageBooking::class);
    }

    public function otherServices(): HasMany
    {
        return $this->hasMany(OtherService::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
