<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripAutomationLog extends Model
{
    use HasUuid;

    protected $table = 'trip_automation_log';

    protected $fillable = ['trip_id', 'action', 'status', 'result'];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
