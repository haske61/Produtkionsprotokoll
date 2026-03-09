<?php

namespace App\Filament\Resources\Deliveries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DeliveryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('date')
                    ->label('Datum')
                    ->required()
                    ->default(now()),
                TextInput::make('crm_nummer')
                    ->label('CRM-Nummer')
                    ->required(),
                Select::make('origin')
                    ->label('Herkunft')
                    ->options([
                        'Elfenbeinküste' => 'Elfenbeinküste',
                        'Nigeria' => 'Nigeria',
                        'Ecuador' => 'Ecuador',
                        'Andere' => 'Andere',
                    ])
                    ->required()
                    ->searchable(),
                Select::make('silo')
                    ->label('Silo')
                    ->options([
                        '1' => 'Silo 1 (Linie 1)',
                        '2' => 'Silo 2 (Linie 1)',
                        '3' => 'Silo 3 (Linie 1)',
                        '4' => 'Silo 4 (Linie 1)',
                        '11' => 'Silo 11 (Linie 1)',
                        '12' => 'Silo 12 (Linie 1)',
                        '5' => 'Silo 5 (Linie 2)',
                        '6' => 'Silo 6 (Linie 2)',
                        '7' => 'Silo 7 (Linie 2)',
                        '8' => 'Silo 8 (Linie 2)',
                    ])
                    ->required()
                    ->searchable(),
                TextInput::make('quantity_kg')
                    ->label('Menge (kg)')
                    ->required()
                    ->numeric()
                    ->suffix('kg'),
                Textarea::make('notes')
                    ->label('Bemerkungen')
                    ->columnSpanFull(),
            ]);
    }
}
