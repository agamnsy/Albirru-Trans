<?php

namespace App\Filament\Resources\Galeris\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Columns\TextColumn;
// use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;

class GalerisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('id')
                //     ->label('ID'),
                //     // ->searchable()
                // ImageColumn::make('media')
                //     ->label('Preview Galeri')
                //     ->getStateUsing(fn ($record) => $record->media[0] ?? null)
                //     ->disk('public')
                //     ->circular()
                //     ->size(56)
                //     ->ring(8),
                TextColumn::make('judul')
                    ->label('Judul Galeri')
                    ->searchable(), 
                TextColumn::make('kategori_label')
                    ->label('Kategori')
                    ->badge()
                    ->getStateUsing(function ($record) {

                        return $record->kategori?->nama ?? 'Tidak Berkategori';
                
                    })
                
                    ->color(function ($record) {
                
                        return $record->kategori?->warna ?? 'gray';
                
                    })
                    // ->color(fn ($record) => $record->kategori?->warna)
                    ->size('lg')
                    ->searchable(),
                TextColumn::make('nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->searchable(),
                TextColumn::make('tanggal_penyewaan')
                    ->label('Tanggal Penyewaan')
                    ->date('d F Y'),
                TextColumn::make('created_at')
                    ->label('Tanggal Upload')
                    ->dateTime('d F Y')
                    ->description(fn ($record) => $record->created_at->format('H:i'))
                    // ->sortable(),
                    // TextColumn::make('tanggal_selesai')
                    // ->date()
                    // ->sortable(),
                    // TextColumn::make('updated_at')
                    //     ->dateTime()
                    //     ->sortable()
                    //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make()
                    ->label('Data Terhapus')
                    ->native(false),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchPlaceholder('Cari judul/kategori')
            ->filtersTriggerAction(fn ($action) => $action->label('Filter'))
            ->filtersApplyAction(fn ($action) => $action->label('Terapkan Filter'))
            ->emptyStateHeading('Belum ada galeri yang ditambahkan')
            ->emptyStateDescription('Silakan tambahkan galeri terlebih dahulu.')
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->size('lg')
                    ->modalHeading('Ubah Galeri')
                    ->modalWidth('2xl')
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalSubmitActionLabel('Simpan Perubahan')
                    ->successNotificationTitle('Berhasil mengubah data'),
                DeleteAction::make()
                    ->iconButton()
                    ->size('lg')
                    ->modalHeading('Hapus Data Galeri?')
                    ->modalDescription('Apakah Anda yakin ingin menghapus data galeri?')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Galeri telah dihapus'),
                RestoreAction::make()
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('Pulihkan')
                    ->modalHeading('Pulihkan Data Galeri?')
                    ->modalDescription('Data galeri yang dipulihkan akan kembali muncul pada daftar galeri.')
                    ->modalSubmitActionLabel('Ya, Pulihkan')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Galeri berhasil dipulihkan'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->modalHeading('Hapus Beberapa Data Galeri?')
                        ->modalDescription('Apakah Anda yakin ingin menghapus data galeri yang dipilih?')
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
