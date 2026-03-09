<?php

namespace App\Filament\Resources\ProductionLogs\Pages;

use App\Filament\Resources\ProductionLogs\ProductionLogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProductionLog extends EditRecord
{
    protected static string $resource = ProductionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
