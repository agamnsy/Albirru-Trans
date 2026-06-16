<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Armada;
use App\Models\Pelanggan;
use App\Models\Penyewaan;

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;
    
    protected function getStats(): array
    {
        $dataPenyewaan = collect(range(6, 0))->map(function ($days) {
            return Penyewaan::whereDate('created_at', now()->subDays($days))->count();
        })->toArray();

        $armadaTersediaHariIni = Armada::where('status', 'tersedia')
            ->whereDoesntHave('penyewaans', function ($query) {
                $query->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
                    ->whereDate('tanggal_mulai', '<=', today())
                    ->whereDate('tanggal_selesai', '>=', today());
            })
            ->count();

        $armadaDisewaHariIni = Armada::where('status', 'tersedia')
            ->whereHas('penyewaans', function ($query) {
                $query->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
                    ->whereDate('tanggal_mulai', '<=', today())
                    ->whereDate('tanggal_selesai', '>=', today());
            })
            ->count();
        
        return [
            Stat::make('Total Pelanggan', Pelanggan::count())
                ->description('Pelanggan terdaftar')
                ->color('primary'),

            Stat::make('Armada Tersedia Hari Ini', $armadaTersediaHariIni)
                ->description('Siap jalan')
                ->color('success'),

            Stat::make('Armada Disewa Hari Ini', $armadaDisewaHariIni)
                ->description('Penyewaan aktif hari ini')
                ->color('danger'),

            Stat::make('Total Penyewaan', Penyewaan::count())
                ->chart($dataPenyewaan)
                ->description('Seluruh data penyewaan')
                ->color('primary'),
        ];
    }
}