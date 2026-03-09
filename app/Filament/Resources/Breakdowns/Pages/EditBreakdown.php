<?php

namespace App\Filament\Resources\Breakdowns\Pages;

use App\Filament\Resources\Breakdowns\BreakdownResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBreakdown extends EditRecord
{
    protected static string $resource = BreakdownResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
