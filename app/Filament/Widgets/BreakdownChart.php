<?php

namespace App\Filament\Widgets;

use App\Models\Breakdown;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class BreakdownChart extends Widget
{
    protected string $view = 'filament.widgets.breakdown-chart';
    protected static ?int $sort = 4;
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
            $start = $anchor->copy()->subDays(29);
            $end = $anchor;

            $gouda = $this->queryByDay($start, $end, 1);
            $tornado = $this->queryByDay($start, $end, 2);
            $total = $this->queryByDay($start, $end, null);

            $labels = [];
            $goudaData = [];
            $tornadoData = [];
            $totalData = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = $anchor->copy()->subDays($i);
                $key = $date->format('Y-m-d');
                $labels[] = $date->format('d.m');
                $goudaData[] = $gouda->get($key)?->minutes ?? 0;
                $tornadoData[] = $tornado->get($key)?->minutes ?? 0;
                $totalData[] = $total->get($key)?->minutes ?? 0;
            }
        } elseif ($this->filterMode === 'week') {
            $anchor = Carbon::parse($this->filterWeek ?: now()->startOfWeek()->format('Y-m-d'))->startOfWeek();
            $start = $anchor->copy()->subWeeks(11);
            $end = $anchor->copy()->endOfWeek();

            $gouda = $this->queryByWeek($start, $end, 1);
            $tornado = $this->queryByWeek($start, $end, 2);
            $total = $this->queryByWeek($start, $end, null);

            $labels = [];
            $goudaData = [];
            $tornadoData = [];
            $totalData = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = $anchor->copy()->subWeeks($i);
                $key = $date->format('oW');
                $labels[] = 'KW ' . $date->format('W');
                $goudaData[] = $gouda->get($key)?->minutes ?? 0;
                $tornadoData[] = $tornado->get($key)?->minutes ?? 0;
                $totalData[] = $total->get($key)?->minutes ?? 0;
            }
        } else {
            $anchor = Carbon::parse(($this->filterMonth ?: now()->format('Y-m')) . '-01');
            $start = $anchor->copy()->subMonths(11)->startOfMonth();
            $end = $anchor->copy()->endOfMonth();

            $gouda = $this->queryByMonth($start, $end, 1);
            $tornado = $this->queryByMonth($start, $end, 2);
            $total = $this->queryByMonth($start, $end, null);

            $labels = [];
            $goudaData = [];
            $tornadoData = [];
            $totalData = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = $anchor->copy()->subMonths($i);
                $labels[] = $date->translatedFormat('M Y');
                $gMatch = $gouda->first(fn ($r) => $r->year == $date->year && $r->month == $date->month);
                $tMatch = $tornado->first(fn ($r) => $r->year == $date->year && $r->month == $date->month);
                $aMatch = $total->first(fn ($r) => $r->year == $date->year && $r->month == $date->month);
                $goudaData[] = $gMatch?->minutes ?? 0;
                $tornadoData[] = $tMatch?->minutes ?? 0;
                $totalData[] = $aMatch?->minutes ?? 0;
            }
        }

        return [
            'type' => 'bar',
            'data' => [
                'datasets' => [
                    [
                        'label' => 'Gouda (min)',
                        'data' => $goudaData,
                        'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                        'borderColor' => 'rgb(34, 197, 94)',
                        'borderWidth' => 1,
                    ],
                    [
                        'label' => 'Tornado (min)',
                        'data' => $tornadoData,
                        'backgroundColor' => 'rgba(234, 179, 8, 0.7)',
                        'borderColor' => 'rgb(234, 179, 8)',
                        'borderWidth' => 1,
                    ],
                    [
                        'label' => 'Gesamt (min)',
                        'data' => $totalData,
                        'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
                        'borderColor' => 'rgb(239, 68, 68)',
                        'borderWidth' => 1,
                        'type' => 'line',
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

    private function baseQuery(?int $lineId)
    {
        $query = Breakdown::query()
            ->join('machines', 'breakdowns.machine_id', '=', 'machines.id');

        if ($lineId) {
            $query->where('machines.production_line_id', $lineId);
        }

        return $query;
    }

    private function queryByDay(Carbon $start, Carbon $end, ?int $lineId)
    {
        return $this->baseQuery($lineId)
            ->selectRaw('DATE(breakdowns.date) as period, SUM(breakdowns.breakdown_minutes) as minutes')
            ->where('breakdowns.date', '>=', $start)
            ->where('breakdowns.date', '<=', $end)
            ->groupByRaw('DATE(breakdowns.date)')
            ->get()->keyBy('period');
    }

    private function queryByWeek(Carbon $start, Carbon $end, ?int $lineId)
    {
        return $this->baseQuery($lineId)
            ->selectRaw('YEARWEEK(breakdowns.date, 1) as period, SUM(breakdowns.breakdown_minutes) as minutes')
            ->where('breakdowns.date', '>=', $start)
            ->where('breakdowns.date', '<=', $end)
            ->groupByRaw('YEARWEEK(breakdowns.date, 1)')
            ->get()->keyBy('period');
    }

    private function queryByMonth(Carbon $start, Carbon $end, ?int $lineId)
    {
        return $this->baseQuery($lineId)
            ->selectRaw('YEAR(breakdowns.date) as year, MONTH(breakdowns.date) as month, SUM(breakdowns.breakdown_minutes) as minutes')
            ->where('breakdowns.date', '>=', $start)
            ->where('breakdowns.date', '<=', $end)
            ->groupByRaw('YEAR(breakdowns.date), MONTH(breakdowns.date)')
            ->get();
    }
}
