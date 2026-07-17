<?php

namespace App\Models;

use App\Models\WhatsAppLog;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trip extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'trip_number', 'customer_id', 'status', 'type', 'name',
        'destination', 'start_date', 'end_date',
        'latitude', 'longitude',
        'total_selling_price', 'total_cost_price', 'currency',
        'notes', 'internal_notes', 'created_by',
    ];

    protected $with = ['customer', 'passengers'];

    protected $appends = ['profit', 'profit_margin'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_selling_price' => 'decimal:2',
            'total_cost_price' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function passengers(): HasMany
    {
        return $this->hasMany(Passenger::class);
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

    public function tripNotes(): HasMany
    {
        return $this->hasMany(TripNote::class);
    }

    public function timeline(): HasMany
    {
        return $this->hasMany(TripTimelineEvent::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(TripExpense::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(EmailLog::class);
    }

    public function whatsappMessages(): HasMany
    {
        return $this->hasMany(WhatsAppLog::class);
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(TripBenefit::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function paymentSchedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logTimeline(string $type, string $description, array $metadata = []): TripTimelineEvent
    {
        return $this->timeline()->create([
            'type' => $type,
            'description' => $description,
            'user_id' => auth()->id(),
            'metadata' => $metadata,
        ]);
    }

    public function recalculateTotals(): void
    {
        $selling = $this->services->sum('selling_price');
        $cost = $this->services->sum('cost_price');

        // Legacy fallback: also sum from old individual tables
        $selling += collect()
            ->merge($this->flightSegments()->pluck('selling_price'))
            ->merge($this->hotelBookings()->pluck('selling_price'))
            ->merge($this->transferBookings()->pluck('selling_price'))
            ->merge($this->visaApplications()->pluck('selling_price'))
            ->merge($this->insurancePolicies()->pluck('selling_price'))
            ->merge($this->activities()->pluck('selling_price'))
            ->merge($this->cruiseBookings()->pluck('selling_price'))
            ->merge($this->trainBookings()->pluck('selling_price'))
            ->merge($this->carRentals()->pluck('selling_price'))
            ->merge($this->packageBookings()->pluck('selling_price'))
            ->merge($this->otherServices()->pluck('selling_price'))
            ->sum();

        $cost += collect()
            ->merge($this->flightSegments()->pluck('cost_price'))
            ->merge($this->hotelBookings()->pluck('cost_price'))
            ->merge($this->transferBookings()->pluck('cost_price'))
            ->merge($this->visaApplications()->pluck('cost_price'))
            ->merge($this->insurancePolicies()->pluck('cost_price'))
            ->merge($this->activities()->pluck('cost_price'))
            ->merge($this->cruiseBookings()->pluck('cost_price'))
            ->merge($this->trainBookings()->pluck('cost_price'))
            ->merge($this->carRentals()->pluck('cost_price'))
            ->merge($this->packageBookings()->pluck('cost_price'))
            ->merge($this->otherServices()->pluck('cost_price'))
            ->sum();

        $this->updateQuietly([
            'total_selling_price' => $selling,
            'total_cost_price' => $cost,
        ]);
    }

    // ─── Profit Breakdown ──────────────────────────────────

    public function getRevenueByType(string $type): float
    {
        return $this->services()->where('type', $type)->sum('selling_price');
    }

    public function getCostByType(string $type): float
    {
        return $this->services()->where('type', $type)->sum('cost_price');
    }

    public function getProfitBreakdownAttribute(): array
    {
        $breakdown = [];
        foreach (Service::TYPES as $key => $label) {
            $revenue = $this->getRevenueByType($key);
            $cost = $this->getCostByType($key);
            if ($revenue > 0 || $cost > 0) {
                $breakdown[$key] = [
                    'label'    => $label,
                    'revenue'  => $revenue,
                    'cost'     => $cost,
                    'profit'   => $revenue - $cost,
                    'margin'   => $cost > 0 ? (($revenue - $cost) / $cost) * 100 : 0,
                ];
            }
        }
        return $breakdown;
    }

    public function getCommissionAttribute(): float
    {
        return $this->expenses()->where('type', 'commission')->sum('amount');
    }

    public function getNetProfitAttribute(): float
    {
        return $this->profit - $this->commission;
    }

    // ─── Payment Totals ────────────────────────────────────

    public function getCustomerPaidAttribute(): float
    {
        return $this->paymentSchedules()->customer()->paid()->sum('amount_paid');
    }

    public function getCustomerDueAttribute(): float
    {
        return $this->paymentSchedules()->customer()->pending()->sum('amount')
             - $this->paymentSchedules()->customer()->pending()->sum('amount_paid');
    }

    public function getSupplierPaidAttribute(): float
    {
        return $this->paymentSchedules()->supplier()->paid()->sum('amount_paid');
    }

    public function getSupplierDueAttribute(): float
    {
        return $this->paymentSchedules()->supplier()->pending()->sum('amount')
             - $this->paymentSchedules()->supplier()->pending()->sum('amount_paid');
    }

    public function getProfitAttribute(): float
    {
        return $this->total_selling_price - $this->total_cost_price;
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->total_selling_price > 0) {
            return ($this->profit / $this->total_selling_price) * 100;
        }
        return 0;
    }
}
