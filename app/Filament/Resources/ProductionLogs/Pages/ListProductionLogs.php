<?php

namespace App\Filament\Resources\ProductionLogs\Pages;

use App\Filament\Resources\ProductionLogs\ProductionLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProductionLogs extends ListRecords
{
    protected static string $resource = ProductionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
