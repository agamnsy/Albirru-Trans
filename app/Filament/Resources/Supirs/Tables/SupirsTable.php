<?php

namespace App\Filament\Resources\Supirs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;

class SupirsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Supir')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('no_hp')
                    ->label('No HP')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->size('xl')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'aktif' => 'Aktif',
                        'bertugas' => 'Bertugas',
                        'nonaktif' => 'Nonaktif',
                        default => '-',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'aktif' => 'success',
                        'bertugas' => 'warning',
                        'nonaktif' => 'danger',
                        default => 'gray',
                    })

            ])
            ->filters([
                TrashedFilter::make()
                    ->label('Data Terhapus')
                    ->native(false),
            ])
            ->emptyStateHeading('Belum ada supir yang ditambahkan')
            ->emptyStateDescription('Silakan tambahkan data supir terlebih dahulu.')
            ->searchPlaceholder('Cari supir')
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->icon('heroicon-s-pencil-square')
                    ->size('xl')
                    ->tooltip('Ubah')
                    ->visible(function ($record) {
                        return ! $record->penugasanSupirs()
                            ->whereIn('status', ['ditugaskan', 'diterima'])
                            ->whereHas('penyewaan', function ($query) {
                                $query->whereNotIn('status', ['selesai', 'dibatalkan']);
                            })
                            ->exists();
                    })
                    ->modalHeading('Ubah Supir')
                    ->modalWidth('xl')
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalSubmitActionLabel('Simpan Perubahan')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Data supir berhasil diperbarui'),

                DeleteAction::make()
                    ->iconButton()
                    ->icon('heroicon-s-trash')
                    ->size('xl')
                    ->color('danger')
                    ->tooltip('Hapus')
                    ->visible(function ($record) {
                        return ! $record->penugasanSupirs()
                            ->whereIn('status', ['ditugaskan', 'diterima'])
                            ->whereHas('penyewaan', function ($query) {
                                $query->whereNotIn('status', ['selesai', 'dibatalkan']);
                            })
                            ->exists();
                    })
                    ->modalHeading('Hapus Data Supir?')
                    ->modalDescription('Apakah Anda yakin ingin menghapus data supir ini?')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Data supir berhasil dihapus'),

                RestoreAction::make()
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('Pulihkan')
                    ->modalHeading('Pulihkan Data Supir?')
                    ->modalDescription('Data supir yang dipulihkan akan kembali muncul pada daftar supir.')
                    ->modalSubmitActionLabel('Ya, Pulihkan')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Supir berhasil dipulihkan'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->modalHeading('Hapus Data Supir?')
                        ->modalDescription('Apakah Anda yakin ingin menghapus data supir yang dipilih?')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->modalCancelActionLabel('Batal')
                        ->successNotificationTitle('Data supir berhasil dihapus'),
                    RestoreBulkAction::make()
                        ->label('Pulihkan Terpilih'),
                ])
                ->label('Aksi'),
            ]);
    }
}
