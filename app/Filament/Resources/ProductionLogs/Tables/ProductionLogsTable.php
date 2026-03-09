<?php

namespace App\Filament\Resources\ProductionLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductionLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('productionLine.name')
                    ->label('Linie')
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Datum')
                    ->date('d.m.Y')
                    ->sortable(),
                TextColumn::make('shift')
                    ->label('Schicht')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'frueh' => 'Frühschicht',
                        'spaet' => 'Spätschicht',
                        'nacht' => 'Nachtschicht',
                        default => '-',
                    })
                    ->sortable(),
                TextColumn::make('silo')
                    ->label('Silo')
                    ->formatStateUsing(fn (?string $state): string => $state ? "Silo $state" : '-')
                    ->sortable(),
                TextColumn::make('beans_processed_kg')
                    ->label('Bohnen (kg)')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kg')
                    ->sortable(),
                TextColumn::make('nibs_produced_kg')
                    ->label('Nibs (kg)')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kg')
                    ->sortable(),
                TextColumn::make('cacao_mass_produced_kg')
                    ->label('Kakaomasse (kg)')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kg')
                    ->sortable(),
                TextColumn::make('breakdowns_count')
                    ->label('Störungen')
                    ->counts('breakdowns')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Erstellt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('production_line_id')
                    ->label('Linie')
                    ->relationship('productionLine', 'name'),
                SelectFilter::make('shift')
                    ->label('Schicht')
                    ->options([
                        'frueh' => 'Frühschicht',
                        'spaet' => 'Spätschicht',
                        'nacht' => 'Nachtschicht',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
