<?php

namespace App\Filament\Resources\ProductionLogs\Pages;

use App\Filament\Resources\ProductionLogs\ProductionLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProductionLog extends CreateRecord
{
    protected static string $resource = ProductionLogResource::class;
}
