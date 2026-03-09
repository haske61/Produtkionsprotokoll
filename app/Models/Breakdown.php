<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Breakdown extends Model
{
    protected $fillable = [
        'production_log_id',
        'machine_id',
        'user_id',
        'date',
        'status',
        'breakdown_minutes',
        'description',
        'resolution',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function productionLog(): BelongsTo
    {
        return $this->belongsTo(ProductionLog::class);
    }
}
