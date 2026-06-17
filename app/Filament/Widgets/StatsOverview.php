<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Armada;
use App\Models\Pelanggan;
use App\Models\Penyewaan;
use Carbon\Carbon;

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;
    
    protected function getStats(): array
    {
        $now = Carbon::now();

        $dataPenyewaan = collect(range(6, 0))->map(function ($days) {
            return Penyewaan::whereDate('created_at', now()->subDays($days))->count();
        })->toArray();

        $armadaTersediaSaatIni = Armada::where('status', 'tersedia')
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

        $armadaDisewaSaatIni = Armada::where('status', 'tersedia')
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

        return [
            Stat::make('Total Pelanggan', Pelanggan::count())
                ->description('Pelanggan terdaftar')
                ->color('primary'),

            Stat::make('Armada Tersedia Saat Ini', $armadaTersediaSaatIni)
                ->description('Siap digunakan saat ini')
                ->color('success'),

            Stat::make('Armada Disewa Saat Ini', $armadaDisewaSaatIni)
                ->description('Sedang dalam penyewaan aktif')
                ->color('danger'),

            Stat::make('Total Penyewaan', Penyewaan::count())
                ->chart($dataPenyewaan)
                ->description('Seluruh data penyewaan')
                ->color('primary'),
        ];
    }
}