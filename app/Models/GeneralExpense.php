<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralExpense extends Model
{
    use SoftDeletes, HasUuid;

    protected $table = 'general_expenses';

    protected $fillable = [
        'category', 'description', 'amount', 'currency',
        'expense_date', 'payment_method', 'reference',
        'vendor', 'notes', 'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
