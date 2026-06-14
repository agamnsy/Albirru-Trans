<?php

namespace App\Filament\Supir\Widgets;

use App\Filament\Supir\Resources\PenugasanSupirs\PenugasanSupirResource;
use App\Models\PenugasanSupir;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RiwayatTugasTerbaruTable extends TableWidget
{
    protected static ?string $heading = 'Riwayat Tugas Terbaru';

    protected static ?string $description = 'Menampilkan 3 riwayat tugas perjalanan terbaru Anda.';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected function getTableQuery(): Builder
    {
        return PenugasanSupir::query()
            ->with([
                'penyewaan.pelanggan',
                'penyewaan.armada',
            ])
            ->where('supir_id', auth()->id())
            ->latest()
            ->limit(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('penyewaan.pelanggan.nama')
                    ->label('Pelanggan')
                    ->placeholder('-'),

                TextColumn::make('penyewaan.armada.nama_bus')
                    ->label('Armada')
                    ->placeholder('-'),

                TextColumn::make('penyewaan.tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date('d F Y'),

                TextColumn::make('penyewaan.tujuan')
                    ->label('Tujuan')
                    ->wrap()
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label('Status Tugas')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'ditugaskan' => 'Ditugaskan',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                        'dibatalkan' => 'Dibatalkan',
                        default => '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'ditugaskan' => 'warning',
                        'diterima' => 'success',
                        'ditolak' => 'danger',
                        'dibatalkan' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('penyewaan.status')
                    ->label('Status Penyewaan')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'dikonfirmasi' => 'Dikonfirmasi',
                        'berjalan' => 'Berjalan',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                        default => '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'dikonfirmasi' => 'success',
                        'berjalan' => 'success',
                        'selesai' => 'primary',
                        'dibatalkan' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->emptyStateHeading('Belum ada riwayat tugas')
            ->emptyStateDescription('Riwayat tugas akan muncul setelah Anda mendapatkan penugasan dari admin.')
            ->emptyStateIcon('heroicon-o-clock')
            ->paginated(false);
    }
}