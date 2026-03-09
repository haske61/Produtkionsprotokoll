<?php

namespace App\Filament\Resources\Breakdowns;

use App\Filament\Resources\Breakdowns\Pages\EditBreakdown;
use App\Filament\Resources\Breakdowns\Pages\ListBreakdowns;
use App\Filament\Resources\Breakdowns\Schemas\BreakdownForm;
use App\Filament\Resources\Breakdowns\Tables\BreakdownsTable;
use App\Models\Breakdown;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BreakdownResource extends Resource
{
    protected static ?string $model = Breakdown::class;

    protected static ?string $navigationLabel = 'Störungen';
    protected static ?string $modelLabel = 'Störung';
    protected static ?string $pluralModelLabel = 'Störungen';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    public static function form(Schema $schema): Schema
    {
        return BreakdownForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BreakdownsTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBreakdowns::route('/'),
            'edit' => EditBreakdown::route('/{record}/edit'),
        ];
    }
}
