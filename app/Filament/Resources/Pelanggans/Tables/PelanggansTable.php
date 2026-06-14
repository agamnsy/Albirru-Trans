<?php

namespace App\Filament\Resources\Pelanggans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;

class PelanggansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->searchable()
                    ->toggleable(false),
                TextColumn::make('no_hp')
                    ->label('Nomor HP')
                    ->searchable()
                    ->toggleable(false),
                TextColumn::make('penyewaans_count')
                    ->label('Total Penyewaan')
                    ->counts('penyewaans')
                    ->toggleable(false),
            ])
            ->filters([
                TrashedFilter::make()
                    ->label('Data Terhapus')
                    ->native(false),
            ])
            ->emptyStateHeading('Belum ada pelanggan yang terdaftar')
            ->emptyStateDescription('Silakan tambahkan pelanggan terlebih dahulu.')
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('Ubah')
                    ->modalWidth('md')
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalHeading('Ubah Data Pelanggan')
                    ->modalCancelActionLabel('Batal')
                    ->modalSubmitActionLabel('Simpan Perubahan')
                    ->successNotificationTitle('Data pelanggan berhasil diperbarui'),
            
                DeleteAction::make()
                    ->iconButton()
                    ->size('lg')
                    ->tooltip('Hapus')
                    ->before(function ($record, $action) {
            
                        $hasActiveBooking = $record->penyewaans()
                            ->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
                            ->exists();
                
                        if ($hasActiveBooking) {
                
                            Notification::make()
                                ->title('Tidak dapat menghapus pelanggan')
                                ->body('Pelanggan ini masih memiliki penyewaan aktif. Selesaikan atau batalkan penyewaan terlebih dahulu.')
                                ->danger()
                                ->duration(5000)
                                ->send();
                
                            $action->cancel();
                        }
                    })
                    ->modalHeading('Hapus Data Pelanggan?')
                    ->modalDescription('Apakah Anda yakin ingin menghapus data pelanggan ini? Data yang dihapus akan masuk ke data terhapus dan dapat dipulihkan kembali.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Berhasil menghapus pelanggan'),
            
                RestoreAction::make()
                    ->iconButton('heroicon-s-arrow-path-rounded-square')
                    ->size('lg')
                    ->tooltip('Pulihkan')
                    ->modalHeading('Pulihkan Data Pelanggan?')
                    ->modalDescription('Data pelanggan yang dipulihkan akan kembali muncul pada daftar pelanggan.')
                    ->modalSubmitActionLabel('Ya, Pulihkan')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Pelanggan berhasil dipulihkan'),
            ])
            ->searchPlaceholder('Cari pelanggan')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->action(function ($records) {

                            $deletedCount = 0;
                            $blockedCount = 0;
                    
                            foreach ($records as $record) {
                    
                                $hasActiveBooking = $record->penyewaans()
                                    ->whereIn('status', ['pending', 'dikonfirmasi'])
                                    ->exists();
                    
                                if ($hasActiveBooking) {
                                    $blockedCount++;
                                    continue;
                                }
                    
                                $record->delete();
                                $deletedCount++;
                            }

                            if ($deletedCount > 0) {
                                Notification::make()
                                    ->title('Berhasil menghapus data')
                                    ->body("{$deletedCount} pelanggan berhasil dihapus")
                                    ->success()
                                    ->duration(4000)
                                    ->send();
                            }
                    
                            if ($blockedCount > 0) {
                                Notification::make()
                                    ->title('Sebagian data tidak dapat dihapus')
                                    ->body("{$blockedCount} pelanggan masih memiliki penyewaan aktif (pending/dikonfirmasi). Selesaikan atau batalkan penyewaan terlebih dahulu.")
                                    ->danger()
                                    ->duration(5000)
                                    ->send();
                            }
                        })
                        ->modalHeading('Hapus Beberapa Data Pelanggan?')
                        ->modalDescription('Apakah Anda yakin ingin menghapus data pelanggan yang dipilih?')
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
