<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionLog extends Model
{
    protected $fillable = [
        'delivery_id',
        'production_line_id',
        'date',
        'beans_processed_kg',
        'nibs_produced_kg',
        'cacao_mass_produced_kg',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'beans_processed_kg' => 'decimal:2',
        'nibs_produced_kg' => 'decimal:2',
        'cacao_mass_produced_kg' => 'decimal:2',
    ];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(Delivery::class);
    }

    public function productionLine(): BelongsTo
    {
        return $this->belongsTo(ProductionLine::class);
    }
}
