<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Armada; // Import Model Armada
use App\Models\Pelanggan; // Import Model Pelanggan
use App\Models\Penyewaan; // Import Model Penyewaan

class StatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '10s';

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;
    
    protected function getStats(): array
    {
        // Ambil data penyewaan 7 hari terakhir
        $dataPenyewaan = collect(range(6, 0))->map(function ($days) {
            return Penyewaan::whereDate('created_at', now()->subDays($days))->count();
        })->toArray();
        
        return [
            // Mengambil jumlah baris dari tabel pelanggans
            Stat::make('Total Pelanggan', Pelanggan::count())
                ->description('Terdaftar')
                ->color('primary'),

            // Mengambil jumlah armada yang statusnya 'tersedia'
            Stat::make('Armada Tersedia', Armada::where('status', 'tersedia')->count())
                ->description('Siap jalan')
                ->color('primary'),

            // Mengambil jumlah armada yang statusnya 'disewa'
            Stat::make('Armada Disewa', Armada::where('status', 'disewa')->count())
                ->description('Dalam perjalanan')
                ->color('primary'),

            // Total semua penyewaan
            Stat::make('Total Penyewaan', Penyewaan::count())
                ->chart($dataPenyewaan)
                ->description('Berjalan dan selesai')
                ->color('primary'),
        ];
    }
}
