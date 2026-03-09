<?php

namespace App\Filament\Resources\Breakdowns\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BreakdownForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('machine_id')
                    ->label('Maschine')
                    ->relationship('machine', 'name')
                    ->disabled()
                    ->preload(),
                DatePicker::make('date')
                    ->label('Datum')
                    ->disabled(),
                TextInput::make('breakdown_minutes')
                    ->label('Stillstandzeit (Minuten)')
                    ->disabled()
                    ->suffix('min'),
                Textarea::make('description')
                    ->label('Beschreibung')
                    ->columnSpanFull(),
            ]);
    }
}
