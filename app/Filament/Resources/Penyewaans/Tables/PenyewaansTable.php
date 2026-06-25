<?php

namespace App\Filament\Resources\Penyewaans\Tables;

use App\Models\User;
use App\Models\Penyewaan;
use App\Models\PenugasanSupir;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon; 
use Illuminate\Support\Facades\DB;

class PenyewaansTable
{
    private static function getWaktuMulai($penyewaan): string
    {
        $tanggal = Carbon::parse($penyewaan->tanggal_mulai)->format('Y-m-d');

        $jam = $penyewaan->jam_mulai
            ? Carbon::parse($penyewaan->jam_mulai)->format('H:i:s')
            : '00:00:00';

        return "{$tanggal} {$jam}";
    }

    private static function getWaktuSelesai($penyewaan): string
    {
        $tanggal = Carbon::parse($penyewaan->tanggal_selesai)->format('Y-m-d');

        $jam = $penyewaan->jam_selesai
            ? Carbon::parse($penyewaan->jam_selesai)->format('H:i:s')
            : '23:59:59';

        return "{$tanggal} {$jam}";
    }

    private static function supirBentrokJadwal($supirId, $penyewaan): bool
    {
        if (! $supirId || ! $penyewaan) {
            return false;
        }

        $waktuMulai = self::getWaktuMulai($penyewaan);
        $waktuSelesai = self::getWaktuSelesai($penyewaan);

        return PenugasanSupir::query()
            ->where('supir_id', $supirId)
            ->whereIn('status', ['ditugaskan', 'diterima'])
            ->whereHas('penyewaan', function ($query) use ($penyewaan, $waktuMulai, $waktuSelesai) {
                $query
                    ->where('id', '!=', $penyewaan->id)
                    ->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
                    ->whereRaw(
                        "TIMESTAMP(tanggal_mulai, COALESCE(jam_mulai, '00:00:00')) < ?",
                        [$waktuSelesai]
                    )
                    ->whereRaw(
                        "TIMESTAMP(tanggal_selesai, COALESCE(jam_selesai, '23:59:59')) > ?",
                        [$waktuMulai]
                    );
            })
            ->exists();
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pelanggan.nama')
                    ->label('Nama Pelanggan')
                    ->searchable()
                    ->toggleable(false),

                TextColumn::make('armada.nama_bus')
                    ->label('Armada')
                    ->searchable()
                    ->toggleable(false),

                TextColumn::make('tanggal_mulai')
                    ->label('Mulai Sewa')
                    ->date('d F Y')
                    ->description(fn ($record) => $record->jam_mulai
                        ? Carbon::parse($record->jam_mulai)->format('H:i') . ' WIB'
                        : '-'
                    )
                    ->toggleable(false)
                    ->sortable(),

                TextColumn::make('tanggal_selesai')
                    ->label('Selesai Sewa')
                    ->date('d F Y')
                    ->description(fn ($record) => $record->jam_selesai
                        ? Carbon::parse($record->jam_selesai)->format('H:i') . ' WIB'
                        : '-'
                    )
                    ->toggleable(false),

                TextColumn::make('status')
                    ->label('Status Penyewaan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'dikonfirmasi' => 'success',
                        'berjalan' => 'success',
                        'selesai' => 'info',
                        'dibatalkan' => 'gray',
                    })
                    ->size('xl')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)) // Merapikan tampilan jadi huruf kapital di awal
                    ->toggleable(false),

                TextColumn::make('penugasanAktif.supir.name')
                    ->label('Supir')
                    ->placeholder('Belum Ditugaskan'),
                
                TextColumn::make('penugasanAktif.status')
                    ->label('Status Penugasan')
                    ->badge()
                    ->size('xl')
                    ->placeholder('Belum Ada')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'ditugaskan' => 'Ditugaskan',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                        'dibatalkan' => 'Dibatalkan',
                        default => 'Belum Ada',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'ditugaskan' => 'warning',
                        'diterima' => 'success',
                        'ditolak' => 'danger',
                        'dibatalkan' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('alamat_penjemputan')
                    ->label('Alamat Penjemputan')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->alamat_penjemputan)
                    ->toggleable(false),

                // TextColumn::make('tujuan')
                //     ->label('Tujuan Destinasi')
                //     ->limit(30)
                //     ->tooltip(fn ($record) => $record->tujuan)
                //     ->toggleable(false),

                // TextColumn::make('created_at')
                //     ->dateTime('d F Y')
                //     ->description(fn ($record) => $record->created_at->format('H:i') . ' WIB')
                //     ->label('Dibuat Pada')
                //     ->toggleable(false),

                // TextColumn::make('updated_at')
                //     ->dateTime('d F Y')
                //     ->description(fn ($record) => $record->updated_at->format('H:i') . ' WIB')
                //     ->label('Terakhir Diubah')
                //     ->toggleable(false),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Penyewaan')
                    ->multiple()
                    ->options([
                        'pending' => 'Pending',
                        'dikonfirmasi' => 'Dikonfirmasi',
                        'berjalan' => 'Berjalan',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->placeholder('Semua Status')
                    ->native(false), // Agar tampilannya konsisten dengan UI Filament lainnya

                SelectFilter::make('bulan')
                    ->label('Bulan')
                    ->placeholder('Semua Bulan')
                    ->multiple()
                    ->native(false)
                    ->options(function () {
                        return collect(range(1, 12))->mapWithKeys(function ($month) {
                            $date = Carbon::create()->month($month);
                
                            return [
                                $date->format('m') => $date->translatedFormat('F'),
                            ];
                        });
                    })
                    ->query(function ($query, array $data) {
                        $values = $data['values'] ?? [];
                
                        if (empty($values)) {
                            return $query;
                        }
                
                        $months = array_map('intval', $values);
                
                        return $query->whereIn(
                            DB::raw('MONTH(tanggal_mulai)'),
                            $months
                        );
                    }),

                SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->placeholder('Semua Tahun')
                    ->native(false)
                    ->options(function () {
                        return Penyewaan::query()
                            ->selectRaw('YEAR(tanggal_mulai) as year')
                            ->distinct()
                            ->pluck('year', 'year')
                            ->toArray();
                    })
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->whereYear('tanggal_mulai', $data['value']);
                        }
                    }),

                TrashedFilter::make()
                    ->label('Data Terhapus')
                    ->native(false),

            ])
            ->defaultSort('created_at', 'desc')
            ->searchPlaceholder('Cari pelanggan')
            ->filtersTriggerAction(fn ($action) => $action->label('Filter'))
            ->filtersApplyAction(fn ($action) => $action->label('Terapkan Filter'))
            ->emptyStateHeading('Belum ada penyewaan yang terjadwal')
            ->emptyStateDescription('Silakan tambahkan armada dan pelanggan terlebih dahulu sebelum membuat penyewaan.')

            ->recordActions([
                Action::make('assignSupir')
                    ->iconButton()
                    ->size('lg')
                    ->icon('heroicon-s-user-plus')
                    ->tooltip('Assign Supir')
                    ->color('primary')
                    ->visible(function ($record) {
                        $sudahAdaSupirAktif = $record->penugasanSupirs()
                            ->whereIn('status', ['ditugaskan', 'diterima'])
                            ->exists();
                    
                        $tanggalMulaiSudahLewat = $record->tanggal_mulai
                            && $record->tanggal_mulai->lt(today());
                    
                        return in_array($record->status, ['pending', 'dikonfirmasi'])
                            && ! $sudahAdaSupirAktif
                            && ! $tanggalMulaiSudahLewat;
                    })
                    ->modalHeading('Assign Supir')
                    ->modalSubmitActionLabel('Assign Supir')
                    ->modalCancelActionLabel('Batal')
                    ->modalWidth('sm')
                    ->form([
                        Select::make('supir_id')
                            ->label('Pilih Supir')
                            ->native(false)
                            ->searchable()
                            ->validationMessages([
                                'required' => 'Silakan pilih supir terlebih dahulu.'
                            ])
                            ->required()
                            ->options(function ($record) {
                                $supirYangSudahMenolak = $record->penugasanSupirs()
                                    ->where('status', 'ditolak')
                                    ->pluck('supir_id');

                                $waktuMulai = self::getWaktuMulai($record);
                                $waktuSelesai = self::getWaktuSelesai($record);

                                return User::query()
                                    ->where('role', 'supir')
                                    ->whereIn('status', ['aktif', 'bertugas'])
                                    ->whereNotIn('id', $supirYangSudahMenolak)
                                    ->whereDoesntHave('penugasanSupirs', function ($query) use ($record, $waktuMulai, $waktuSelesai) {
                                        $query
                                            ->whereIn('status', ['ditugaskan', 'diterima'])
                                            ->whereHas('penyewaan', function ($q) use ($record, $waktuMulai, $waktuSelesai) {
                                                $q
                                                    ->where('id', '!=', $record->id)
                                                    ->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
                                                    ->whereRaw(
                                                        "TIMESTAMP(tanggal_mulai, COALESCE(jam_mulai, '00:00:00')) < ?",
                                                        [$waktuSelesai]
                                                    )
                                                    ->whereRaw(
                                                        "TIMESTAMP(tanggal_selesai, COALESCE(jam_selesai, '23:59:59')) > ?",
                                                        [$waktuMulai]
                                                    );
                                            });
                                    })
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->placeholder('Pilih supir yang tersedia'),
                    ])
                    ->action(function ($record, array $data) {
                        $sudahAdaSupirAktif = $record->penugasanSupirs()
                            ->whereIn('status', ['ditugaskan', 'diterima'])
                            ->exists();
                    
                        if ($sudahAdaSupirAktif) {
                            Notification::make()
                                ->title('Supir sudah ditugaskan')
                                ->body('Penyewaan ini sudah memiliki supir yang sedang ditugaskan.')
                                ->danger()
                                ->send();
                    
                            return;
                        }
                    
                        if (in_array($record->status, ['selesai', 'dibatalkan'])) {
                            Notification::make()
                                ->title('Tidak dapat assign supir')
                                ->body('Penyewaan sudah selesai atau dibatalkan.')
                                ->danger()
                                ->send();
                    
                            return;
                        }
                    
                        if (self::supirBentrokJadwal($data['supir_id'], $record)) {
                            Notification::make()
                                ->title('Supir tidak tersedia')
                                ->body('Supir ini sudah memiliki tugas lain pada jadwal sewa yang bentrok.')
                                ->danger()
                                ->send();
                    
                            return;
                        }
                    
                        PenugasanSupir::create([
                            'penyewaan_id' => $record->id,
                            'supir_id' => $data['supir_id'],
                            'status' => 'ditugaskan',
                            'assigned_at' => now(),
                        ]);
                    
                        $record->update([
                            'status' => 'dikonfirmasi',
                        ]);
                    
                        Notification::make()
                            ->title('Supir berhasil ditugaskan')
                            ->body('Penyewaan telah dikonfirmasi dan menunggu respons supir.')
                            ->success()
                            ->send();
                    }),
                
                Action::make('isiSupirHistoris')
                    ->iconButton()
                    ->size('xl')
                    ->icon('heroicon-s-identification')
                    ->tooltip('Tambahkan Supir')
                    ->color('gray')
                    ->visible(function ($record) {
                        $tanggalMulaiSudahLewat = $record->tanggal_mulai
                            && $record->tanggal_mulai->lt(today());
                
                        $sudahAdaPenugasan = $record->penugasanSupirs()
                            ->whereIn('status', ['ditugaskan', 'diterima', 'dibatalkan'])
                            ->exists();
                
                        return $tanggalMulaiSudahLewat
                            && $record->status === 'selesai'
                            && ! $sudahAdaPenugasan;
                    })
                    ->modalHeading('Tambahkan Supir ke Data')
                    ->modalDescription('Pilih supir yang bertugas pada penyewaan lama ini. Data akan dicatat sebagai riwayat tugas selesai.')
                    ->modalSubmitActionLabel('Simpan Data')
                    ->modalCancelActionLabel('Batal')
                    ->modalWidth('md')
                    ->form([
                        Select::make('supir_id')
                            ->label('Supir yang Bertugas')
                            ->native(false)
                            ->searchable()
                            ->placeholder('Pilih supir')
                            ->options(function () {
                                return User::query()
                                    ->where('role', 'supir')
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->validationMessages([
                                'required' => 'Silakan pilih supir yang bertugas.',
                            ]),
                    ])
                    ->action(function ($record, array $data) {
                        $sudahAdaPenugasan = $record->penugasanSupirs()
                            ->whereIn('status', ['ditugaskan', 'diterima', 'dibatalkan'])
                            ->exists();
                
                        if ($sudahAdaPenugasan) {
                            Notification::make()
                                ->title('Supir sudah tercatat')
                                ->body('Penyewaan ini sudah memiliki data penugasan supir.')
                                ->danger()
                                ->send();
                
                            return;
                        }
                
                        PenugasanSupir::create([
                            'penyewaan_id' => $record->id,
                            'supir_id' => $data['supir_id'],
                            'status' => 'diterima',
                            'assigned_at' => now(),
                            'responded_at' => now(),
                        ]);
                
                        Notification::make()
                            ->title('Penambahan supir berhasil disimpan')
                            ->body('Data supir yang bertugas pada penyewaan lama berhasil dicatat.')
                            ->success()
                            ->send();
                    }),

                ViewAction::make()
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('Lihat Detail'),

                EditAction::make()
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('Ubah')
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalHeading('Ubah Penyewaan')
                    ->modalSubmitActionLabel('Simpan Perubahan')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Data penyewaan berhasil diperbarui'),

                DeleteAction::make()
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('Hapus')
                    ->visible(fn ($record) => in_array($record->status, ['selesai', 'dibatalkan']))
                    ->modalHeading('Hapus Penyewaan?')
                    ->modalDescription('Apakah Anda yakin ingin menghapus data penyewaan ini? Data yang terhapus dapat dipulihkan kembali.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Berhasil menghapus penyewaan'),
                
                RestoreAction::make()
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('Pulihkan')
                    ->modalHeading('Pulihkan Data Penyewaan?')
                    ->modalDescription('Data penyewaan yang dipulihkan akan kembali muncul pada daftar penyewaan.')
                    ->modalSubmitActionLabel('Ya, Pulihkan')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Penyewaan berhasil dipulihkan'),

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->modalHeading('Hapus Data Penyewaan?')
                        ->modalDescription('Data penyewaan yang masih aktif tidak akan dihapus.')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->modalCancelActionLabel('Batal')
                        ->action(function ($records) {
                            $deletedCount = 0;
                            $blockedCount = 0;

                            foreach ($records as $record) {
                                if (in_array($record->status, ['pending', 'dikonfirmasi', 'berjalan'])) {
                                    $blockedCount++;
                                    continue;
                                }

                                $record->delete();
                                $deletedCount++;
                            }

                            if ($deletedCount > 0) {
                                Notification::make()
                                    ->title('Berhasil menghapus data')
                                    ->body("{$deletedCount} data penyewaan berhasil dihapus.")
                                    ->success()
                                    ->send();
                            }

                            if ($blockedCount > 0) {
                                Notification::make()
                                    ->title('Sebagian data tidak dapat dihapus')
                                    ->body("{$blockedCount} penyewaan masih aktif sehingga tidak dapat dihapus.")
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->successNotification(null),

                    RestoreBulkAction::make()
                        ->label('Pulihkan Terpilih'),
                ])
            ->label('Aksi')
            ]);
    }
}