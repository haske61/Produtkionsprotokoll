<?php

namespace App\Filament\Resources\Deliveries\Pages;

use App\Filament\Resources\Deliveries\DeliveryResource;
use App\Models\SiloReset;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListDeliveries extends ListRecords
{
    protected static string $resource = DeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('resetSilo')
                ->label('Silo zurücksetzen')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->form([
                    Select::make('silo')
                        ->label('Silo auswählen')
                        ->options([
                            '1' => 'Silo 1 (Gouda)',
                            '2' => 'Silo 2 (Gouda)',
                            '3' => 'Silo 3 (Gouda)',
                            '4' => 'Silo 4 (Gouda)',
                            '11' => 'Silo 11 (Gouda)',
                            '12' => 'Silo 12 (Gouda)',
                            '5' => 'Silo 5 (Tornado)',
                            '6' => 'Silo 6 (Tornado)',
                            '7' => 'Silo 7 (Tornado)',
                            '8' => 'Silo 8 (Tornado)',
                        ])
                        ->required(),
                ])
                ->requiresConfirmation()
                ->modalHeading('Silo zurücksetzen')
                ->modalDescription('Der Silobestand wird auf 0 kg zurückgesetzt. Bisherige Lieferungen bleiben erhalten.')
                ->modalSubmitActionLabel('Zurücksetzen')
                ->action(function (array $data): void {
                    SiloReset::create([
                        'silo' => $data['silo'],
                        'reset_at' => now(),
                    ]);
                }),
        ];
    }
}
