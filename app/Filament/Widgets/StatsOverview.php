<?php

namespace App\Filament\Widgets;

use App\Models\Delivery;
use App\Models\ProductionLog;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class StatsOverview extends Widget
{
    protected string $view = 'filament.widgets.stats-overview';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public string $filterMode = 'month';
    public string $filterDate = '';
    public string $filterWeek = '';
    public string $filterMonth = '';

    public function mount(): void
    {
        $this->filterDate = now()->format('Y-m-d');
        $this->filterWeek = now()->startOfWeek()->format('Y-m-d');
        $this->filterMonth = now()->format('Y-m');
    }

    public function getWeekOptions(): array
    {
        $options = [];
        for ($i = 0; $i < 20; $i++) {
            $date = now()->subWeeks($i)->startOfWeek();
            $options[$date->format('Y-m-d')] = $i === 0
                ? 'KW ' . $date->format('W') . ' (aktuell)'
                : 'KW ' . $date->format('W') . ' / ' . $date->format('Y');
        }
        return $options;
    }

    public function getMonthOptions(): array
    {
        $options = [];
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $options[$date->format('Y-m')] = $i === 0 ? 'Dieser Monat' : $date->translatedFormat('F Y');
        }
        return $options;
    }

    public function getStats(): array
    {
        $deliveryQuery = Delivery::query();
        $productionQuery = ProductionLog::query();

        if ($this->filterMode === 'day') {
            $date = Carbon::parse($this->filterDate ?: now()->format('Y-m-d'));
            $deliveryQuery->whereDate('date', $date);
            $productionQuery->whereDate('date', $date);
            $period = $date->format('d.m.Y');
        } elseif ($this->filterMode === 'week') {
            $start = Carbon::parse($this->filterWeek ?: now()->startOfWeek()->format('Y-m-d'))->startOfWeek();
            $end = $start->copy()->endOfWeek();
            $deliveryQuery->whereBetween('date', [$start, $end]);
            $productionQuery->whereBetween('date', [$start, $end]);
            $period = 'KW ' . $start->format('W') . ' / ' . $start->format('Y');
        } else {
            $date = Carbon::parse(($this->filterMonth ?: now()->format('Y-m')) . '-01');
            $deliveryQuery->whereMonth('date', $date->month)->whereYear('date', $date->year);
            $productionQuery->whereMonth('date', $date->month)->whereYear('date', $date->year);
            $period = $date->translatedFormat('F Y');
        }

        return [
            [
                'label' => 'Lieferungen',
                'value' => $deliveryQuery->count(),
                'description' => $period,
                'icon' => 'heroicon-o-truck',
            ],
            [
                'label' => 'Gelieferte Menge',
                'value' => number_format($deliveryQuery->sum('quantity_kg'), 0, ',', '.') . ' kg',
                'description' => $period,
                'icon' => 'heroicon-o-scale',
            ],
            [
                'label' => 'Nibs produziert',
                'value' => number_format($productionQuery->sum('nibs_produced_kg'), 0, ',', '.') . ' kg',
                'description' => $period,
                'icon' => 'heroicon-o-fire',
            ],
            [
                'label' => 'Kakaomasse produziert',
                'value' => number_format($productionQuery->sum('cacao_mass_produced_kg'), 0, ',', '.') . ' kg',
                'description' => $period,
                'icon' => 'heroicon-o-beaker',
            ],
        ];
    }
}
