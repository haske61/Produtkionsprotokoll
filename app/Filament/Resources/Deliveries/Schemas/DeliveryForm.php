<?php

namespace App\Filament\Resources\Deliveries\Schemas;

use Filament\Forms\Components\DatePicker;
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
                TextInput::make('supplier')
                    ->label('Lieferant'),
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
