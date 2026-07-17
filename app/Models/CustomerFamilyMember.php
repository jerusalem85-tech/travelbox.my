<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerFamilyMember extends Model
{
    use HasUuid;

    protected $fillable = ['customer_id', 'name', 'relationship', 'date_of_birth', 'passport_number'];

    protected function casts(): array
    {
        return ['date_of_birth' => 'date'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
