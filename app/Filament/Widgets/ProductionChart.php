<?php

namespace App\Filament\Widgets;

use App\Models\ProductionLog;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class ProductionChart extends Widget
{
    protected string $view = 'filament.widgets.production-chart';
    protected static ?int $sort = 3;

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

    public function getFilterKey(): string
    {
        return match ($this->filterMode) {
            'day' => $this->filterDate,
            'week' => $this->filterWeek,
            default => $this->filterMonth,
        };
    }

    public function getChartConfig(): array
    {
        if ($this->filterMode === 'day') {
            $anchor = Carbon::parse($this->filterDate ?: now()->format('Y-m-d'));
            $data = ProductionLog::query()
                ->selectRaw('DATE(date) as period, SUM(beans_processed_kg) as beans, SUM(nibs_produced_kg) as nibs, SUM(cacao_mass_produced_kg) as mass')
                ->where('date', '>=', $anchor->copy()->subDays(29))
                ->where('date', '<=', $anchor)
                ->groupByRaw('DATE(date)')
                ->orderByRaw('DATE(date)')
                ->get()->keyBy('period');

            $labels = [];
            $beans = [];
            $nibs = [];
            $mass = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = $anchor->copy()->subDays($i);
                $key = $date->format('Y-m-d');
                $labels[] = $date->format('d.m');
                $match = $data->get($key);
                $beans[] = $match ? round($match->beans, 2) : 0;
                $nibs[] = $match ? round($match->nibs, 2) : 0;
                $mass[] = $match ? round($match->mass, 2) : 0;
            }
        } elseif ($this->filterMode === 'week') {
            $anchor = Carbon::parse($this->filterWeek ?: now()->startOfWeek()->format('Y-m-d'))->startOfWeek();
            $data = ProductionLog::query()
                ->selectRaw('YEARWEEK(date, 1) as period, SUM(beans_processed_kg) as beans, SUM(nibs_produced_kg) as nibs, SUM(cacao_mass_produced_kg) as mass')
                ->where('date', '>=', $anchor->copy()->subWeeks(11))
                ->where('date', '<=', $anchor->copy()->endOfWeek())
                ->groupByRaw('YEARWEEK(date, 1)')
                ->orderByRaw('YEARWEEK(date, 1)')
                ->get()->keyBy('period');

            $labels = [];
            $beans = [];
            $nibs = [];
            $mass = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = $anchor->copy()->subWeeks($i);
                $key = $date->format('oW');
                $labels[] = 'KW ' . $date->format('W');
                $match = $data->get($key);
                $beans[] = $match ? round($match->beans, 2) : 0;
                $nibs[] = $match ? round($match->nibs, 2) : 0;
                $mass[] = $match ? round($match->mass, 2) : 0;
            }
        } else {
            $anchor = Carbon::parse(($this->filterMonth ?: now()->format('Y-m')) . '-01');
            $data = ProductionLog::query()
                ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(beans_processed_kg) as beans, SUM(nibs_produced_kg) as nibs, SUM(cacao_mass_produced_kg) as mass')
                ->where('date', '>=', $anchor->copy()->subMonths(11)->startOfMonth())
                ->where('date', '<=', $anchor->copy()->endOfMonth())
                ->groupByRaw('YEAR(date), MONTH(date)')
                ->orderByRaw('YEAR(date), MONTH(date)')
                ->get();

            $labels = [];
            $beans = [];
            $nibs = [];
            $mass = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = $anchor->copy()->subMonths($i);
                $labels[] = $date->translatedFormat('M Y');
                $match = $data->first(fn ($row) => $row->year == $date->year && $row->month == $date->month);
                $beans[] = $match ? round($match->beans, 2) : 0;
                $nibs[] = $match ? round($match->nibs, 2) : 0;
                $mass[] = $match ? round($match->mass, 2) : 0;
            }
        }

        return [
            'type' => 'bar',
            'data' => [
                'datasets' => [
                    [
                        'label' => 'Bohnen verarbeitet (kg)',
                        'data' => $beans,
                        'borderColor' => 'rgb(139, 92, 42)',
                        'backgroundColor' => 'rgba(139, 92, 42, 0.7)',
                        'borderWidth' => 1,
                    ],
                    [
                        'label' => 'Nibs produziert (kg)',
                        'data' => $nibs,
                        'borderColor' => 'rgb(180, 130, 70)',
                        'backgroundColor' => 'rgba(180, 130, 70, 0.7)',
                        'borderWidth' => 1,
                    ],
                    [
                        'label' => 'Kakaomasse produziert (kg)',
                        'data' => $mass,
                        'borderColor' => 'rgb(80, 40, 10)',
                        'backgroundColor' => 'rgba(80, 40, 10, 0.7)',
                        'borderWidth' => 1,
                    ],
                ],
                'labels' => $labels,
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
            ],
        ];
    }
}
