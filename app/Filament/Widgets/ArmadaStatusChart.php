<?php

namespace App\Filament\Widgets;

use App\Models\Armada;
use Filament\Widgets\ChartWidget;

class ArmadaStatusChart extends ChartWidget
{
    protected ?string $heading = 'Status Ketersedian Armada';

    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Menghitung data berdasarkan kolom 'status' di tabel armadas
        $tersedia = Armada::where('status', 'tersedia')->count();
        $disewa = Armada::where('status', 'disewa')->count();
        $servis = Armada::where('status', 'maintenance')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Armada',
                    'data' => [$tersedia, $disewa, $servis],
                    'backgroundColor' => [
                        '#10b981', // Hijau (Success) - Tersedia
                        '#ef4444', // Merah (Danger) - Disewa
                        '#f59e0b', // Kuning (Warning) - Servis
                    ],
                    // 'hoverOffset' => 4,
                ],
            ],
            'labels' => ['Tersedia', 'Disewa', 'Dalam Perbaikan'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
