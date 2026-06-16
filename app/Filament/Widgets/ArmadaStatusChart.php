<?php

namespace App\Filament\Widgets;

use App\Models\Armada;
use Filament\Widgets\ChartWidget;

class ArmadaStatusChart extends ChartWidget
{
    protected ?string $heading = 'Status Ketersediaan Armada';

    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $tersedia = Armada::where('status', 'tersedia')
            ->whereDoesntHave('penyewaans', function ($query) {
                $query->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
                    ->whereDate('tanggal_mulai', '<=', today())
                    ->whereDate('tanggal_selesai', '>=', today());
            })
            ->count();

        $sedangDisewa = Armada::where('status', 'tersedia')
            ->whereHas('penyewaans', function ($query) {
                $query->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
                    ->whereDate('tanggal_mulai', '<=', today())
                    ->whereDate('tanggal_selesai', '>=', today());
            })
            ->count();

        $maintenance = Armada::where('status', 'maintenance')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Armada',
                    'data' => [$tersedia, $sedangDisewa, $maintenance],
                    'backgroundColor' => [
                        '#10b981',
                        '#ef4444',
                        '#f59e0b',
                    ],
                ],
            ],
            'labels' => [
                'Tersedia Hari Ini',
                'Sedang Disewa Hari Ini',
                'Maintenance',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}