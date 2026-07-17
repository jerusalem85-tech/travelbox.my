<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    use HasUuid;

    protected $fillable = ['name', 'type', 'content', 'is_default'];

    protected function casts(): array
    {
        return [
            'content' => 'json',
            'is_default' => 'boolean',
        ];
    }
}
