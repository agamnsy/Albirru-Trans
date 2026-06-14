<?php

namespace App\Filament\Supir\Widgets;

use App\Filament\Supir\Resources\PenugasanSupirs\PenugasanSupirResource;
use App\Models\AktivitasPerjalanan;
use App\Models\PenugasanSupir;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TugasAktifTable extends TableWidget
{
    protected static ?string $heading = 'Tugas Aktif Saat Ini';

    protected static ?string $description = 'Daftar tugas perjalanan yang sedang menunggu konfirmasi atau sedang berjalan.';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected function getTableQuery(): Builder
    {
        return PenugasanSupir::query()
            ->with([
                'penyewaan.pelanggan',
                'penyewaan.armada',
            ])
            ->where('supir_id', auth()->id())
            ->whereIn('status', ['ditugaskan', 'diterima'])
            ->whereHas('penyewaan', function ($query) {
                $query->whereIn('status', ['dikonfirmasi', 'berjalan']);
            })
            ->latest();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->searchable(false)
            ->columns([
                TextColumn::make('penyewaan.pelanggan.nama')
                    ->label('Pelanggan')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('penyewaan.armada.nama_bus')
                    ->label('Armada')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('penyewaan.tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->date('d F Y'),

                TextColumn::make('penyewaan.tanggal_selesai')
                    ->label('Tanggal Selesai')
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
                        default => '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'ditugaskan' => 'warning',
                        'diterima' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('penyewaan.status')
                    ->label('Status Penyewaan')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'dikonfirmasi' => 'Dikonfirmasi',
                        'berjalan' => 'Berjalan',
                        default => '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'dikonfirmasi' => 'primary',
                        'berjalan' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('progress')
                    ->label('Progress')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        $last = AktivitasPerjalanan::query()
                            ->where('penyewaan_id', $record->penyewaan_id)
                            ->latest()
                            ->first();

                        return $last?->status ?? 'belum_dimulai';
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'belum_dimulai' => 'Belum Dimulai',
                        'sampai_penjemputan' => 'Sampai Penjemputan',
                        'mulai_perjalanan' => 'Mulai Perjalanan',
                        'sampai_tujuan' => 'Sampai Tujuan',
                        'perjalanan_pulang' => 'Perjalanan Pulang',
                        'sampai_garasi' => 'Sampai Garasi',
                        default => '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'belum_dimulai' => 'gray',
                        'sampai_penjemputan' => 'warning',
                        'mulai_perjalanan' => 'success',
                        'sampai_tujuan' => 'success',
                        'perjalanan_pulang' => 'success',
                        'sampai_garasi' => 'primary',
                        default => 'gray',
                    }),
            ])
            ->recordActions([
                // Action::make('detailPerjalanan')
                //     ->label('Detail Perjalanan')
                //     ->icon('heroicon-s-eye')
                //     ->color('gray')
                //     ->url(fn ($record) => PenugasanSupirResource::getUrl('view', [
                //         'record' => $record,
                //     ])),
            ])
            ->emptyStateHeading('Belum ada tugas aktif')
            ->emptyStateDescription('Tugas perjalanan akan muncul setelah admin menetapkan Anda sebagai supir pada penyewaan.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list')
            ->paginated(false);
    }
}