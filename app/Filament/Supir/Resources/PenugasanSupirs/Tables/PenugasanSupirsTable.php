<?php

namespace App\Filament\Supir\Resources\PenugasanSupirs\Tables;

use App\Models\AktivitasPerjalanan;
use App\Models\Penyewaan;

use Illuminate\Support\Facades\DB;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Carbon\Carbon;

class PenugasanSupirsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('penyewaan.pelanggan.nama')
                    ->label('Nama Pelanggan')
                    ->searchable(),

                TextColumn::make('penyewaan.armada.nama_bus')
                    ->label('Armada')
                    ->searchable(),

                TextColumn::make('penyewaan.tanggal_mulai')
                    ->label('Mulai Sewa')
                    ->date('d F Y')
                    ->description(fn ($record) => $record->penyewaan?->jam_mulai
                        ? Carbon::parse($record->penyewaan->jam_mulai)->format('H:i') . ' WIB'
                        : '-'
                    ),
                    // ->sortable(),

                TextColumn::make('penyewaan.tanggal_selesai')
                    ->label('Selesai Sewa')
                    ->date('d F Y')
                    ->description(fn ($record) => $record->penyewaan?->jam_selesai
                        ? Carbon::parse($record->penyewaan->jam_selesai)->format('H:i') . ' WIB'
                        : '-'
                    ),
                    // ->sortable(),

                TextColumn::make('penyewaan.tujuan')
                    ->label('Tujuan Destinasi')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->penyewaan?->tujuan),

                TextColumn::make('status')
                    ->label('Status Penugasan')
                    ->badge()
                    ->size('xl')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ditugaskan' => 'Ditugaskan',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                        'dibatalkan' => 'Dibatalkan',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'ditugaskan' => 'warning',
                        'diterima' => 'success',
                        'ditolak' => 'danger',
                        'dibatalkan' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('penyewaan.status')
                    ->label('Status Penyewaan')
                    ->badge()
                    ->size('xl')
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
                
                TextColumn::make('progres_perjalanan')
                    ->label('Progres Perjalanan')
                    ->badge()
                    ->size('xl')
                    ->getStateUsing(function ($record) {
                
                        // Kalau penyewaan sudah selesai, tampilkan progres terakhir sebagai sampai garasi
                        if ($record->penyewaan?->status === 'selesai') {
                            return 'sampai_garasi';
                        }
                
                        // Kalau penyewaan dibatalkan
                        if ($record->penyewaan?->status === 'dibatalkan') {
                            return 'dibatalkan';
                        }
                
                        $last = \App\Models\AktivitasPerjalanan::query()
                            ->where('penyewaan_id', $record->penyewaan_id)
                            ->latest()
                            ->first();
                
                        return $last?->status ?? 'belum_dimulai';
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'belum_dimulai' => 'Belum Dimulai',
                        'sampai_penjemputan' => 'Sampai Penjemputan',
                        'mulai_perjalanan' => 'Mulai Perjalanan',
                        'sampai_tujuan' => 'Sampai Tujuan',
                        'perjalanan_pulang' => 'Perjalanan Pulang',
                        'sampai_garasi' => 'Sampai Garasi',
                        'dibatalkan' => 'Dibatalkan',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'belum_dimulai' => 'gray',
                        'sampai_penjemputan' => 'warning',
                        'mulai_perjalanan' => 'success',
                        'sampai_tujuan' => 'success',
                        'perjalanan_pulang' => 'success',
                        'sampai_garasi' => 'primary',
                        'dibatalkan' => 'gray',
                        default => 'gray',
                    }),
                    
                TextColumn::make('assigned_at')
                    ->label('Ditugaskan Pada')
                    ->dateTime('d F Y')
                    ->description(fn ($record) => $record->assigned_at?->format('H:i')),
            ])
            
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Tugas')
                    ->native(false)
                    ->placeholder('Semua')
                    ->options([
                        'ditugaskan' => 'Ditugaskan',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
            
                SelectFilter::make('bulan')
                    ->label('Bulan')
                    ->multiple()
                    ->native(false)
                    ->options([
                        '01' => 'Januari',
                        '02' => 'Februari',
                        '03' => 'Maret',
                        '04' => 'April',
                        '05' => 'Mei',
                        '06' => 'Juni',
                        '07' => 'Juli',
                        '08' => 'Agustus',
                        '09' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ])
                    ->query(function ($query, array $data) {
                        $values = $data['values'] ?? [];
            
                        if (empty($values)) {
                            return $query;
                        }
            
                        $months = array_map('intval', $values);
            
                        return $query->whereHas('penyewaan', function ($query) use ($months) {
                            $query->whereIn(
                                DB::raw('MONTH(tanggal_mulai)'),
                                $months
                            );
                        });
                    }),
            
                SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->native(false)
                    ->options(function () {
                        return Penyewaan::query()
                            ->selectRaw('YEAR(tanggal_mulai) as tahun')
                            ->distinct()
                            ->orderByDesc('tahun')
                            ->pluck('tahun', 'tahun')
                            ->toArray();
                    })
                    ->query(function ($query, array $data) {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }
            
                        return $query->whereHas('penyewaan', function ($query) use ($data) {
                            $query->whereYear('tanggal_mulai', $data['value']);
                        });
                    }),
            ])

            ->recordActions([
                Action::make('terima')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('primary')
                    ->visible(fn ($record) =>
                        $record->status === 'ditugaskan'
                        && $record->penyewaan?->status !== 'dibatalkan'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Terima Tugas Penyewaan?')
                    ->modalDescription('Apakah Anda yakin ingin menerima tugas penyewaan ini?')
                    ->modalSubmitActionLabel('Ya, Terima')
                    ->modalCancelActionLabel('Batal')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'diterima',
                            'responded_at' => now(),
                        ]);

                        $record->penyewaan->update([
                            'status' => 'berjalan',
                        ]);

                        $record->supir->update([
                            'status' => 'bertugas',
                        ]);

                        Notification::make()
                            ->title('Tugas diterima')
                            ->body('Status penyewaan berhasil diubah menjadi berjalan.')
                            ->success()
                            ->send();
                    }),

                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) =>
                        $record->status === 'ditugaskan'
                        && $record->penyewaan?->status !== 'dibatalkan'
                    )
                    ->modalHeading('Tolak Tugas')
                    ->modalSubmitActionLabel('Tolak Tugas')
                    ->modalCancelActionLabel('Batal')
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalWidth('md')
                    ->form([
                        Textarea::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->placeholder('Masukkan alasan menolak tugas')
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'ditolak',
                            'alasan_penolakan' => $data['alasan_penolakan'],
                            'responded_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Tugas ditolak')
                            ->body('Alasan penolakan berhasil dikirim ke admin.')
                            ->danger()
                            ->send();
                    }),
                    
                Action::make('updatePerjalanan')
                    ->label(function ($record) {
                        $last = AktivitasPerjalanan::query()
                            ->where('penyewaan_id', $record->penyewaan_id)
                            ->latest()
                            ->first();
                
                        return match ($last?->status) {
                            null => 'Sampai Penjemputan',
                            'sampai_penjemputan' => 'Mulai Perjalanan',
                            'mulai_perjalanan' => 'Sampai Tujuan',
                            'sampai_tujuan' => 'Perjalanan Pulang',
                            'perjalanan_pulang' => 'Sampai Garasi',
                            default => 'Progress Selesai',
                        };
                    })
                    ->icon('heroicon-s-map-pin')
                    ->color('primary')
                    ->visible(function ($record) {
                        if ($record->status !== 'diterima') {
                            return false;
                        }
                
                        if (in_array($record->penyewaan?->status, ['selesai', 'dibatalkan'])) {
                            return false;
                        }
                
                        $last = AktivitasPerjalanan::query()
                            ->where('penyewaan_id', $record->penyewaan_id)
                            ->latest()
                            ->first();
                        
                        return $last?->status !== 'sampai_garasi';
                    })
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalWidth('lg')
                    ->modalHeading(function ($record) {
                        $last = AktivitasPerjalanan::query()
                            ->where('penyewaan_id', $record->penyewaan_id)
                            ->latest()
                            ->first();
                
                        return match ($last?->status) {
                            null => 'Sudah Sampai di Penjemputan?',
                            'sampai_penjemputan' => 'Sudah Mulai Perjalanan?',
                            'mulai_perjalanan' => 'Sudah Sampai di Tujuan?',
                            'sampai_tujuan' => 'Sudah Perjalanan Pulang?',
                            'perjalanan_pulang' => 'Sudah Sampai di Garasi?',
                            default => 'Progress perjalanan sudah selesai.',
                        };
                    })
                    ->modalDescription(function ($record) {
                        $last = AktivitasPerjalanan::query()
                            ->where('penyewaan_id', $record->penyewaan_id)
                            ->latest()
                            ->first();
                
                        return match ($last?->status) {
                            null => 'Konfirmasi bahwa Anda sudah sampai di titik penjemputan.',
                            'sampai_penjemputan' => 'Konfirmasi bahwa perjalanan sudah dimulai.',
                            'mulai_perjalanan' => 'Konfirmasi bahwa Anda sudah sampai di tujuan.',
                            'sampai_tujuan' => 'Konfirmasi bahwa perjalanan pulang sudah dimulai.',
                            'perjalanan_pulang' => 'Konfirmasi bahwa Anda sudah sampai di garasi.',
                            default => 'Progress perjalanan sudah selesai.',
                        };
                    })
                    ->modalSubmitActionLabel('Update Progres')
                    ->modalCancelActionLabel('Batal')
                    ->form([
                        FileUpload::make('foto')
                            ->label(function ($record) {
                                $last = AktivitasPerjalanan::query()
                                    ->where('penyewaan_id', $record->penyewaan_id)
                                    ->latest()
                                    ->first();
                    
                                $statusBerikutnya = match ($last?->status) {
                                    null => 'sampai_penjemputan',
                                    'sampai_penjemputan' => 'mulai_perjalanan',
                                    'mulai_perjalanan' => 'sampai_tujuan',
                                    'sampai_tujuan' => 'perjalanan_pulang',
                                    'perjalanan_pulang' => 'sampai_garasi',
                                    default => null,
                                };
                    
                                return $statusBerikutnya === 'sampai_garasi'
                                    ? 'Foto Kondisi Akhir Bus (Maks. 5 foto)'
                                    : 'Foto Dokumentasi (Maks. 5 foto)';
                            })
                            ->multiple()
                            ->maxFiles(5)
                            ->disk('public')
                            ->directory('aktivitas-perjalanan')
                            ->image()
                            ->panelLayout('grid')
                            ->imagePreviewHeight('180')
                            ->helperText(function ($record) {
                                $last = AktivitasPerjalanan::query()
                                    ->where('penyewaan_id', $record->penyewaan_id)
                                    ->latest()
                                    ->first();
                    
                                return $last?->status === 'perjalanan_pulang'
                                    ? 'Upload foto kondisi akhir bus saat sudah sampai garasi.'
                                    : 'Upload foto dokumentasi perjalanan jika diperlukan.';
                            })
                            ->required(function ($record) {
                                $last = AktivitasPerjalanan::query()
                                    ->where('penyewaan_id', $record->penyewaan_id)
                                    ->latest()
                                    ->first();
                    
                                return $last?->status === 'perjalanan_pulang';
                            })
                            ->validationMessages([
                                'required' => 'Foto kondisi akhir bus wajib diunggah saat sampai garasi.',
                            ]),
                    
                        Textarea::make('catatan')
                            ->label(function ($record) {
                                $last = AktivitasPerjalanan::query()
                                    ->where('penyewaan_id', $record->penyewaan_id)
                                    ->latest()
                                    ->first();
                    
                                return $last?->status === 'perjalanan_pulang'
                                    ? 'Catatan Akhir Perjalanan'
                                    : 'Catatan Perjalanan';
                            })
                            ->placeholder('Tambahkan catatan jika diperlukan.')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Catatan bersifat opsional dan akan terlihat oleh admin.'),
                    ])
                    ->action(function ($record, array $data) {
                        $last = AktivitasPerjalanan::query()
                            ->where('penyewaan_id', $record->penyewaan_id)
                            ->latest()
                            ->first();
                
                        $statusBaru = match ($last?->status) {
                            null => 'sampai_penjemputan',
                            'sampai_penjemputan' => 'mulai_perjalanan',
                            'mulai_perjalanan' => 'sampai_tujuan',
                            'sampai_tujuan' => 'perjalanan_pulang',
                            'perjalanan_pulang' => 'sampai_garasi',
                            default => null,
                        };
                
                        if (! $statusBaru) {
                            return;
                        }
                
                        AktivitasPerjalanan::create([
                            'penyewaan_id' => $record->penyewaan_id,
                            'supir_id' => auth()->id(),
                            'status' => $statusBaru,
                            'foto' => $data['foto'] ?? [],
                            'catatan' => $data['catatan'] ?? null,
                        ]);
                
                        Notification::make()
                            ->success()
                            ->title('Progres berhasil diperbarui')
                            ->body('Status perjalanan berhasil diperbarui.')
                            ->send();
                    }),
                    
                Action::make('akhiriPenyewaan')
                    ->label('Akhiri Penyewaan')
                    ->icon('heroicon-s-check-circle')
                    ->color('primary')
                    ->visible(function ($record) {
                        if ($record->status !== 'diterima') {
                            return false;
                        }
                
                        if (in_array($record->penyewaan?->status, ['selesai', 'dibatalkan'])) {
                            return false;
                        }
                
                        $last = AktivitasPerjalanan::query()
                            ->where('penyewaan_id', $record->penyewaan_id)
                            ->latest()
                            ->first();
                
                        return $last?->status === 'sampai_garasi';
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Akhiri Penyewaan?')
                    ->modalDescription('Pastikan progres perjalanan sudah sampai garasi dan foto kondisi akhir bus sudah diunggah.')
                    ->modalSubmitActionLabel('Ya, Akhiri Penyewaan')
                    ->modalCancelActionLabel('Batal')
                    ->action(function ($record) {
                        $record->penyewaan->update([
                            'status' => 'selesai',
                        ]);
                
                        Notification::make()
                            ->success()
                            ->title('Penyewaan selesai')
                            ->body('Penyewaan berhasil diakhiri.')
                            ->send();
                    }),

                ViewAction::make()
                    ->label('Detail Perjalanan')
                    ->icon('heroicon-s-eye')
                    ->color('gray'),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ])
            ->defaultSort('assigned_at', 'desc');;
    }
}
