<?php

namespace App\Filament\Resources\ProductionLogs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductionLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('production_line_id')
                    ->label('Produktionslinie')
                    ->relationship('productionLine', 'name')
                    ->required(),
                Select::make('delivery_id')
                    ->label('Lieferung')
                    ->relationship('delivery')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->date->format('d.m.Y')} – {$record->crm_nummer} ({$record->quantity_kg} kg)")
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('date')
                    ->label('Datum')
                    ->required()
                    ->default(now()),
                TextInput::make('beans_processed_kg')
                    ->label('Bohnen verarbeitet (kg)')
                    ->required()
                    ->numeric()
                    ->suffix('kg')
                    ->default(0),
                TextInput::make('nibs_produced_kg')
                    ->label('Nibs produziert (kg)')
                    ->required()
                    ->numeric()
                    ->suffix('kg')
                    ->default(0),
                TextInput::make('cacao_mass_produced_kg')
                    ->label('Kakaomasse produziert (kg)')
                    ->required()
                    ->numeric()
                    ->suffix('kg')
                    ->default(0),
                Textarea::make('notes')
                    ->label('Bemerkungen')
                    ->columnSpanFull(),
            ]);
    }
}
