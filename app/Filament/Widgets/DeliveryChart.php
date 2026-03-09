<?php

namespace App\Filament\Widgets;

use App\Models\Delivery;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class DeliveryChart extends Widget
{
    protected string $view = 'filament.widgets.delivery-chart';
    protected static ?int $sort = 2;

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
            $data = Delivery::query()
                ->selectRaw('DATE(date) as period, SUM(quantity_kg) as total')
                ->where('date', '>=', $anchor->copy()->subDays(29))
                ->where('date', '<=', $anchor)
                ->groupByRaw('DATE(date)')
                ->orderByRaw('DATE(date)')
                ->pluck('total', 'period');

            $labels = [];
            $values = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = $anchor->copy()->subDays($i);
                $labels[] = $date->format('d.m');
                $values[] = round($data->get($date->format('Y-m-d'), 0), 2);
            }
        } elseif ($this->filterMode === 'week') {
            $anchor = Carbon::parse($this->filterWeek ?: now()->startOfWeek()->format('Y-m-d'))->startOfWeek();
            $data = Delivery::query()
                ->selectRaw('YEARWEEK(date, 1) as period, SUM(quantity_kg) as total')
                ->where('date', '>=', $anchor->copy()->subWeeks(11))
                ->where('date', '<=', $anchor->copy()->endOfWeek())
                ->groupByRaw('YEARWEEK(date, 1)')
                ->orderByRaw('YEARWEEK(date, 1)')
                ->pluck('total', 'period');

            $labels = [];
            $values = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = $anchor->copy()->subWeeks($i);
                $labels[] = 'KW ' . $date->format('W');
                $values[] = round($data->get($date->format('oW'), 0), 2);
            }
        } else {
            $anchor = Carbon::parse(($this->filterMonth ?: now()->format('Y-m')) . '-01');
            $data = Delivery::query()
                ->selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(quantity_kg) as total')
                ->where('date', '>=', $anchor->copy()->subMonths(11)->startOfMonth())
                ->where('date', '<=', $anchor->copy()->endOfMonth())
                ->groupByRaw('YEAR(date), MONTH(date)')
                ->orderByRaw('YEAR(date), MONTH(date)')
                ->get();

            $labels = [];
            $values = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = $anchor->copy()->subMonths($i);
                $labels[] = $date->translatedFormat('M Y');
                $match = $data->first(fn ($row) => $row->year == $date->year && $row->month == $date->month);
                $values[] = $match ? round($match->total, 2) : 0;
            }
        }

        return [
            'type' => 'line',
            'data' => [
                'datasets' => [
                    [
                        'label' => 'Lieferungen (kg)',
                        'data' => $values,
                        'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                        'borderColor' => 'rgb(245, 158, 11)',
                        'borderWidth' => 2,
                        'fill' => true,
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
