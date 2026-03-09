<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiloReset extends Model
{
    protected $fillable = ['silo', 'reset_at'];

    protected $casts = [
        'reset_at' => 'datetime',
    ];

    public static function lastResetFor(string $silo): ?string
    {
        return static::where('silo', $silo)
            ->latest('reset_at')
            ->value('reset_at');
    }
}
