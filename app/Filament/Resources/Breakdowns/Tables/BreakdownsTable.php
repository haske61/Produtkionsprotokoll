<?php

namespace App\Filament\Resources\Breakdowns\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;

class BreakdownsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('machine.name')
                    ->label('Maschine')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('breakdown_minutes')
                    ->label('Stillstand')
                    ->suffix(' min')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('productionLog.productionLine.name')
                    ->label('Produktionslinie')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Datum')
                    ->date('d.m.Y')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Gemeldet von')
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Beschreibung')
                    ->limit(50),
                TextColumn::make('created_at')
                    ->label('Erstellt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Filter::make('date')
                    ->form([
                        DatePicker::make('date')
                            ->label('Datum'),
                    ])
                    ->query(fn ($query, array $data) => $query->when($data['date'], fn ($q, $date) => $q->whereDate('date', $date))),
                SelectFilter::make('production_line')
                    ->label('Produktionslinie')
                    ->options(fn () => \App\Models\ProductionLine::pluck('name', 'id')->toArray())
                    ->query(fn ($query, array $data) => $query->when($data['value'], fn ($q, $lineId) => $q->whereHas('machine', fn ($q) => $q->where('production_line_id', $lineId)))),
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
