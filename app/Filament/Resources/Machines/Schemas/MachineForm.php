<?php

namespace App\Filament\Resources\Machines\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MachineForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Maschinenname')
                    ->required(),
                Select::make('production_line_id')
                    ->label('Produktionslinie')
                    ->relationship('productionLine', 'name')
                    ->required()
                    ->preload(),
                Textarea::make('description')
                    ->label('Beschreibung')
                    ->columnSpanFull(),
            ]);
    }
}
