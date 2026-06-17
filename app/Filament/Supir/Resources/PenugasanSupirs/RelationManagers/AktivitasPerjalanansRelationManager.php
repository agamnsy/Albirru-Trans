<?php

namespace App\Filament\Supir\Resources\PenugasanSupirs\RelationManagers;

use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class AktivitasPerjalanansRelationManager extends RelationManager
{
    protected static string $relationship = 'aktivitasPerjalanans';

    protected static ?string $title = 'Aktivitas Perjalanan';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status')
            ->columns([
                TextColumn::make('status')
                    ->label('Status Perjalanan')
                    ->badge()
                    ->size('xl')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'sampai_penjemputan' => 'Sampai Penjemputan',
                        'mulai_perjalanan' => 'Mulai Perjalanan',
                        'sampai_tujuan' => 'Sampai Tujuan',
                        'perjalanan_pulang' => 'Perjalanan Pulang',
                        'sampai_garasi' => 'Sampai Garasi',
                        default => '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'sampai_penjemputan' => 'warning',
                        'mulai_perjalanan' => 'success',
                        'sampai_tujuan' => 'success',
                        'perjalanan_pulang' => 'success',
                        'sampai_garasi' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('catatan')
                    ->label('Catatan Perjalanan')
                    ->placeholder('Tidak ada catatan')
                    ->wrap(),

                ImageColumn::make('foto')
                    ->label('Foto Dokumentasi')
                    ->disk('public')
                    ->stacked()
                    ->circular()
                    ->size(48)
                    ->placeholder('Tidak ada foto dokumentasi')
                    ->limit(5),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d F Y')
                    ->description(fn ($record) => $record->created_at?->format('H:i')),

                TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime('d F Y')
                    ->description(fn ($record) => $record->updated_at?->format('H:i')),

            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->emptyStateHeading('Belum ada progres perjalanan')
            ->emptyStateDescription('Progres perjalanan akan muncul setelah status perjalanan diperbarui.')
            ->emptyStateIcon('heroicon-o-map-pin')
            ->filters([])
            ->headerActions([])
            ->recordActions([
                EditAction::make('editDokumentasi')
                    ->label('Ubah')
                    ->icon('heroicon-s-pencil-square')
                    ->color('primary')
                    ->size('xl')
                    ->modalHeading('Ubah Dokumentasi Perjalanan')
                    ->modalDescription('Perbarui catatan atau foto dokumentasi perjalanan jika terdapat kesalahan.')
                    ->modalSubmitActionLabel('Simpan Perubahan')
                    ->modalCancelActionLabel('Batal')
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalWidth('lg')
                    ->visible(function ($record) {
                        return $record->supir_id === auth()->id()
                            && $record->penyewaan?->status !== 'dibatalkan';
                    })
                    ->form([
                        FileUpload::make('foto')
                            ->label('Foto Dokumentasi')
                            ->multiple()
                            ->maxFiles(5)
                            ->minFiles(fn ($record) => $record?->status === 'sampai_garasi' ? 1 : null)
                            ->required(fn ($record) => $record?->status === 'sampai_garasi')
                            ->disk('public')
                            ->directory('aktivitas-perjalanan')
                            ->image()
                            ->panelLayout('grid')
                            ->imagePreviewHeight('140')
                            ->helperText(function ($record) {
                                if ($record?->status === 'sampai_garasi') {
                                    return 'Foto wajib tersedia minimal 1. Anda boleh mengganti foto, tetapi tidak boleh mengosongkannya.';
                                }

                                return 'Tambah atau hapus foto untuk memperbarui dokumentasi.';
                            })
                            ->validationMessages([
                                'required' => 'Foto dokumentasi wajib diunggah untuk status sampai garasi.',
                                'minFiles' => 'Foto dokumentasi tidak boleh kosong untuk status sampai garasi.',
                            ]),

                        Textarea::make('catatan')
                            ->label('Catatan Perjalanan')
                            ->placeholder('Tambahkan atau perbarui catatan perjalanan jika diperlukan.')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Catatan bersifat opsional dan akan terlihat oleh admin.'),
                    ])
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Dokumentasi diperbarui')
                            ->body('Catatan atau foto dokumentasi perjalanan berhasil diperbarui.')
                    ),
            ])
            ->toolbarActions([]);
    }
}