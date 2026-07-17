<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentNumbering extends Model
{
    protected $fillable = [
        'type', 'prefix', 'next_number', 'padding', 'separator', 'use_year',
    ];

    protected function casts(): array
    {
        return [
            'next_number' => 'integer',
            'padding'     => 'integer',
            'use_year'    => 'boolean',
        ];
    }

    public function generateNumber(): string
    {
        $year = $this->use_year ? date('Y') : '';
        $number = str_pad($this->next_number, $this->padding, '0', STR_PAD_LEFT);

        $formatted = $this->prefix;
        if ($year) {
            $formatted .= $this->separator . $year;
        }
        $formatted .= $this->separator . $number;

        $this->increment('next_number');

        return $formatted;
    }

    public static function forType(string $type): string
    {
        $rule = static::firstOrCreate(
            ['type' => $type],
            [
                'prefix'      => strtoupper(substr($type, 0, 3)),
                'next_number' => 1,
                'padding'     => 6,
                'separator'   => '-',
                'use_year'    => true,
            ]
        );

        return $rule->generateNumber();
    }
}
