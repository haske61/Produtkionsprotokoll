<?php

namespace App\Filament\Resources\Breakdowns\Pages;

use App\Filament\Resources\Breakdowns\BreakdownResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBreakdown extends CreateRecord
{
    protected static string $resource = BreakdownResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();

        return $data;
    }
}
