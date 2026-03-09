<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionLog extends Model
{
    protected $fillable = [
        'production_line_id',
        'silo',
        'machine_id',
        'breakdown_minutes',
        'date',
        'shift',
        'beans_processed_kg',
        'nibs_produced_kg',
        'cacao_mass_produced_kg',
        'yield_doppelbohnen',
        'yield_steine',
        'yield_schalen_in_nibs',
        'yield_nibs_in_schalen',
        'yield_feuchtigkeit_nibs',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'beans_processed_kg' => 'decimal:2',
        'nibs_produced_kg' => 'decimal:2',
        'cacao_mass_produced_kg' => 'decimal:2',
        'yield_doppelbohnen' => 'decimal:2',
        'yield_steine' => 'decimal:2',
        'yield_schalen_in_nibs' => 'decimal:2',
        'yield_nibs_in_schalen' => 'decimal:2',
        'yield_feuchtigkeit_nibs' => 'decimal:2',
    ];

    public function productionLine(): BelongsTo
    {
        return $this->belongsTo(ProductionLine::class);
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function breakdowns(): HasMany
    {
        return $this->hasMany(Breakdown::class);
    }
}
