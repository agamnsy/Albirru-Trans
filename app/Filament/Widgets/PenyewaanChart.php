<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Penyewaan;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class PenyewaanChart extends ChartWidget
{
    protected ?string $heading = 'Tren Penyewaan Bulanan';

    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Data 6 bulan terakhir
        $data = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->translatedFormat('F');
            
            // Hitung jumlah penyewaan di bulan tersebut
            $data[] = Penyewaan::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Penyewaan',
                    'data' => $data,
                    'fill' => 'start',
                    'tension' => 0.4, // Membuat garis melengkung (smooth)
                    'borderColor' => '#196FEB',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                // 'x' => [
                //     'grid' => ['display' => false], // Hilangkan garis vertikal
                // ],
                'y' => [
                    'ticks' => [
                        'precision' => 0, // Memaksa angka bulat (tanpa desimal)
                        'stepSize' => 1,  // Lonjakan angka per 1 satuan (1, 2, 3...)
                    ],
                    'beginAtZero' => true, // Grafik selalu mulai dari angka 0
                ],
            ],
        ];
    }
}
