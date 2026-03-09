<?php

namespace App\Filament\Resources\ProductionLogs\Schemas;

use App\Models\Delivery;
use App\Models\ProductionLog;
use App\Models\SiloReset;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Set;
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
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('silo', null)),
                Select::make('silo')
                    ->label('Silo')
                    ->options(function (callable $get) {
                        return match ($get('production_line_id')) {
                            1, '1' => [
                                '1' => 'Silo 1',
                                '2' => 'Silo 2',
                                '3' => 'Silo 3',
                                '4' => 'Silo 4',
                                '11' => 'Silo 11',
                                '12' => 'Silo 12',
                            ],
                            2, '2' => [
                                '5' => 'Silo 5',
                                '6' => 'Silo 6',
                                '7' => 'Silo 7',
                                '8' => 'Silo 8',
                            ],
                            default => [],
                        };
                    })
                    ->required()
                    ->searchable()
                    ->disabled(fn (callable $get) => ! $get('production_line_id')),
                DatePicker::make('date')
                    ->label('Datum')
                    ->required()
                    ->default(now()),
                Select::make('shift')
                    ->label('Schicht')
                    ->options([
                        'frueh' => 'Frühschicht (6–14)',
                        'spaet' => 'Spätschicht (14–22)',
                        'nacht' => 'Nachtschicht (22–6)',
                    ])
                    ->required()
                    ->default(function () {
                        $hour = now()->hour;
                        if ($hour >= 6 && $hour < 14) {
                            return 'frueh';
                        } elseif ($hour >= 14 && $hour < 22) {
                            return 'spaet';
                        }
                        return 'nacht';
                    }),
                TextInput::make('beans_processed_kg')
                    ->label('Bohnen verarbeitet (kg)')
                    ->required()
                    ->numeric()
                    ->suffix('kg')
                    ->default(0)
                    ->rules([
                        fn (callable $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            $silo = $get('silo');
                            if (! $silo || $value <= 0) {
                                return;
                            }

                            $deliveryQuery = Delivery::where('silo', $silo);
                            $productionQuery = ProductionLog::where('silo', $silo);

                            $lastReset = SiloReset::lastResetFor($silo);
                            if ($lastReset) {
                                $deliveryQuery->where('created_at', '>', $lastReset);
                                $productionQuery->where('created_at', '>', $lastReset);
                            }

                            $stock = $deliveryQuery->sum('quantity_kg') - $productionQuery->sum('beans_processed_kg');

                            if ($value > $stock) {
                                $fail("Silo {$silo} hat nur " . round($stock, 2) . " kg Bestand. Entnahme von {$value} kg nicht möglich.");
                            }
                        },
                    ]),
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
                Section::make('Ausbeutestudie')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        TextInput::make('yield_doppelbohnen')
                            ->label('Doppelbohnen')
                            ->numeric()
                            ->suffix('kg'),
                        TextInput::make('yield_steine')
                            ->label('Steine')
                            ->numeric()
                            ->suffix('kg'),
                        TextInput::make('yield_schalen_in_nibs')
                            ->label('Schalen in Nibs')
                            ->numeric()
                            ->suffix('%'),
                        TextInput::make('yield_nibs_in_schalen')
                            ->label('Nibs in Schalen')
                            ->numeric()
                            ->suffix('%'),
                        TextInput::make('yield_feuchtigkeit_nibs')
                            ->label('Feuchtigkeit Nibs')
                            ->numeric()
                            ->suffix('%'),
                    ])
                    ->columnSpanFull(),
                Section::make('Störungen')
                    ->description('Nur ausfüllen bei Maschinenstörung')
                    ->collapsed()
                    ->schema([
                        Repeater::make('breakdowns')
                            ->relationship()
                            ->schema([
                                Select::make('machine_id')
                                    ->label('Maschine')
                                    ->options(function (callable $get) {
                                        $lineId = $get('../../production_line_id');
                                        if (! $lineId) {
                                            return [];
                                        }
                                        return \App\Models\Machine::where('production_line_id', $lineId)
                                            ->pluck('name', 'id')
                                            ->toArray();
                                    })
                                    ->required()
                                    ->searchable(),
                                TextInput::make('breakdown_minutes')
                                    ->label('Stillstandzeit (Minuten)')
                                    ->required()
                                    ->numeric()
                                    ->suffix('min'),
                                Textarea::make('description')
                                    ->label('Bemerkung'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Störung hinzufügen')
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, callable $get): array {
                                $data['user_id'] = auth()->id();
                                $data['date'] = $get('date');
                                $data['status'] = 'offen';
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data, callable $get): array {
                                $data['date'] = $get('date');
                                return $data;
                            }),
                    ])
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->label('Bemerkungen')
                    ->columnSpanFull(),
            ]);
    }
}
