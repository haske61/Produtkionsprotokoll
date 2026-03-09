<?php

namespace App\Filament\Widgets;

use App\Models\Delivery;
use App\Models\ProductionLog;
use App\Models\SiloReset;
use Filament\Widgets\ChartWidget;

class SiloDeliveryChart extends ChartWidget
{
    protected ?string $heading = 'Silobestand gesamt (kg)';
    protected static ?int $sort = 2;
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $silos = ['1', '2', '3', '4', '5', '6', '7', '8', '11', '12'];

        $data = collect();
        foreach ($silos as $silo) {
            $deliveryQuery = Delivery::where('silo', $silo);
            $productionQuery = ProductionLog::where('silo', $silo);

            $lastReset = SiloReset::lastResetFor($silo);
            if ($lastReset) {
                $deliveryQuery->where('created_at', '>', $lastReset);
                $productionQuery->where('created_at', '>', $lastReset);
            }

            $delivered = $deliveryQuery->sum('quantity_kg');
            $used = $productionQuery->sum('beans_processed_kg');
            $data->put($silo, max(0, $delivered - $used));
        }
        $labels = array_map(fn ($s) => "Silo $s", $silos);

        $goudaSilos = ['1', '2', '3', '4', '11', '12'];
        $tornadoSilos = ['5', '6', '7', '8'];

        $goudaValues = array_map(fn ($s) => in_array($s, $goudaSilos) ? round($data->get($s, 0), 2) : null, $silos);
        $tornadoValues = array_map(fn ($s) => in_array($s, $tornadoSilos) ? round($data->get($s, 0), 2) : null, $silos);

        return [
            'datasets' => [
                [
                    'label' => 'Gouda',
                    'data' => $goudaValues,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Tornado',
                    'data' => $tornadoValues,
                    'backgroundColor' => 'rgba(234, 179, 8, 0.7)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
