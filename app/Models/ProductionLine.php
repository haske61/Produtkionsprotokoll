<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionLine extends Model
{
    protected $fillable = ['name'];

    public function productionLogs(): HasMany
    {
        return $this->hasMany(ProductionLog::class);
    }
}
