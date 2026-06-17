<?php

namespace App\Filament\Widgets;

use App\Models\Armada;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class ArmadaStatusChart extends ChartWidget
{
    protected ?string $heading = 'Status Ketersediaan Armada';

    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $now = Carbon::now();

        $tersedia = Armada::where('status', 'tersedia')
            ->whereDoesntHave('penyewaans', function ($query) use ($now) {
                $query->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
                    ->whereRaw(
                        "TIMESTAMP(tanggal_mulai, COALESCE(jam_mulai, '00:00:00')) <= ?",
                        [$now]
                    )
                    ->whereRaw(
                        "TIMESTAMP(tanggal_selesai, COALESCE(jam_selesai, '23:59:59')) >= ?",
                        [$now]
                    );
            })
            ->count();

        $sedangDisewa = Armada::where('status', 'tersedia')
            ->whereHas('penyewaans', function ($query) use ($now) {
                $query->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
                    ->whereRaw(
                        "TIMESTAMP(tanggal_mulai, COALESCE(jam_mulai, '00:00:00')) <= ?",
                        [$now]
                    )
                    ->whereRaw(
                        "TIMESTAMP(tanggal_selesai, COALESCE(jam_selesai, '23:59:59')) >= ?",
                        [$now]
                    );
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
                'Tersedia Saat Ini',
                'Sedang Disewa Saat Ini',
                'Maintenance',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}