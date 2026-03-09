<?php

namespace App\Filament\Resources\Deliveries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DeliveriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Datum')
                    ->date('d.m.Y')
                    ->sortable(),
                TextColumn::make('crm_nummer')
                    ->label('CRM-Nummer')
                    ->searchable(),
                TextColumn::make('origin')
                    ->label('Herkunft')
                    ->searchable(),
                TextColumn::make('silo')
                    ->label('Silo')
                    ->formatStateUsing(fn (string $state): string => "Silo $state")
                    ->sortable(),
                TextColumn::make('quantity_kg')
                    ->label('Menge (kg)')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kg')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Erstellt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                //
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
