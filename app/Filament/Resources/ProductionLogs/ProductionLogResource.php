<?php

namespace App\Filament\Resources\ProductionLogs;

use App\Filament\Resources\ProductionLogs\Pages\CreateProductionLog;
use App\Filament\Resources\ProductionLogs\Pages\EditProductionLog;
use App\Filament\Resources\ProductionLogs\Pages\ListProductionLogs;
use App\Filament\Resources\ProductionLogs\Schemas\ProductionLogForm;
use App\Filament\Resources\ProductionLogs\Tables\ProductionLogsTable;
use App\Models\ProductionLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProductionLogResource extends Resource
{
    protected static ?string $model = ProductionLog::class;

    protected static ?string $navigationLabel = 'Produktionsprotokolle';
    protected static ?string $modelLabel = 'Produktionsprotokoll';
    protected static ?string $pluralModelLabel = 'Produktionsprotokolle';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    public static function form(Schema $schema): Schema
    {
        return ProductionLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductionLogsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductionLogs::route('/'),
            'create' => CreateProductionLog::route('/create'),
            'edit' => EditProductionLog::route('/{record}/edit'),
        ];
    }
}
