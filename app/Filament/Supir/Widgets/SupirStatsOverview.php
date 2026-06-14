<?php

namespace App\Filament\Supir\Widgets;

use App\Models\PenugasanSupir;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SupirStatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;
    
    protected function getStats(): array
    {
        $supirId = auth()->id();

        $tugasBaru = PenugasanSupir::query()
            ->where('supir_id', $supirId)
            ->where('status', 'ditugaskan')
            ->count();

        // $sedangBerjalan = PenugasanSupir::query()
        //     ->where('supir_id', $supirId)
        //     ->where('status', 'diterima')
        //     ->whereHas('penyewaan', function ($query) {
        //         $query->where('status', 'berjalan');
        //     })
        //     ->count();

        $selesai = PenugasanSupir::query()
            ->where('supir_id', $supirId)
            ->whereHas('penyewaan', function ($query) {
                $query->where('status', 'selesai');
            })
            ->count();

        $ditolak = PenugasanSupir::query()
            ->where('supir_id', $supirId)
            ->where('status', 'ditolak')
            ->count();

        return [
            Stat::make('Tugas Baru', $tugasBaru)
                ->description('Menunggu konfirmasi')
                ->color('primary'),

            // Stat::make('Sedang Berjalan', $sedangBerjalan)
            //     ->description('Penyewaan berlangsung')
            //     ->color('primary'),

            Stat::make('Selesai', $selesai)
                ->description('Penyewaan selesai')
                ->color('primary'),

            Stat::make('Ditolak', $ditolak)
                ->description('Tugas ditolak')
                ->color('primary'),
        ];
    }
}