<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripTimelineEvent extends Model
{
    use HasUuid;

    protected $fillable = ['trip_id', 'type', 'description', 'user_id', 'metadata'];

    protected function casts(): array
    {
        return ['metadata' => 'json'];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
