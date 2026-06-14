<?php

namespace App\Filament\Resources\Kategoris\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;

class KategorisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Kategori')
                    ->badge()
                    ->size('xl')
                    ->color(fn ($record) => $record->warna)
                    ->searchable(),
                TextColumn::make('galeris_count')
                    ->label('Jumlah Galeri')
                    ->counts('galeris')
            ])
            ->filters([
                TrashedFilter::make()
                    ->label('Data Terhapus')
                    ->native(false),
            ])
            ->emptyStateHeading('Belum ada kategori yang terdaftar')
            ->emptyStateDescription('Silakan tambahkan kategori terlebih dahulu.')
            ->searchPlaceholder('Cari kategori')
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('Ubah')
                    ->modalWidth('md')
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalHeading('Ubah Kategori')
                    ->modalCancelActionLabel('Batal')
                    ->modalSubmitActionLabel('Simpan Perubahan')
                    ->successNotificationTitle('Kategori berhasil diperbarui'),
                DeleteAction::make()
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('Hapus')
                    ->modalHeading('Hapus Kategori?')
                    ->modalDescription(function ($record) {

                        if ($record->galeris()->count() > 0) {
                
                            return 'Kategori ini sedang digunakan oleh beberapa galeri. Data yang dihapus dapat dipulihkan kembali.';
                        }
                
                        return 'Apakah Anda yakin ingin menghapus kategori ini? Data yang dihapus dapat dipulihkan kembali.';
                    })
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Berhasil menghapus kategori'),
                RestoreAction::make()
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('Pulihkan')
                    ->modalHeading('Pulihkan Data Kategori?')
                    ->modalDescription('Data kategori yang dipulihkan akan kembali muncul pada daftar kategori.')
                    ->modalSubmitActionLabel('Ya, Pulihkan')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Kategori berhasil dipulihkan'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->modalHeading('Hapus Beberapa Kategori?')
                        ->modalDescription('Anda yakin ingin menghapus beberapa kategori ini?')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->modalCancelActionLabel('Batal')
                        ->successNotification(null),
                    RestoreBulkAction::make()
                        ->label('Pulihkan Terpilih'),
                ])
                ->label('Aksi'),
            ]);

    }
}
