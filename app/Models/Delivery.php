<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends Model
{
    protected $fillable = [
        'date',
        'crm_nummer',
        'origin',
        'silo',
        'quantity_kg',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'quantity_kg' => 'decimal:2',
    ];

    public function productionLogs(): HasMany
    {
        return $this->hasMany(ProductionLog::class);
    }
}
