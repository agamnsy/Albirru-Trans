<?php

namespace App\Filament\Resources\Armadas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Tables\Table;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
// use Filament\Actions\ForceDeleteAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;

class ArmadasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->circular()
                    ->label('Foto Armada')
                    ->size(56)
                    ->stacked()
                    ->overlap(4)
                    ->ring(8)
                    ->toggleable(false),
                TextColumn::make('nama_bus')
                    ->searchable()
                    ->label('Nama Bus')
                    ->toggleable(false),
                TextColumn::make('kapasitas')
                    ->numeric()
                    ->toggleable(false),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'tersedia' => 'success',
                        'maintenance' => 'warning',
                        'disewa' => 'danger',
                    })
                    ->size('xl')
                    ->toggleable(false),
                // TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->placeholder('Semua')
                    ->native(false)
                    ->options([
                        'tersedia' => 'Tersedia',
                        'maintenance' => 'Maintenance',
                        'disewa' => 'Disewa',
                    ]),
                    TrashedFilter::make()
                    ->label('Data Terhapus')
                    ->native(false)
            ])
            ->searchPlaceholder('Cari armada')
            ->filtersTriggerAction(fn ($action) => $action->label('Filter'))
            ->filtersApplyAction(fn ($action) => $action->label('Terapkan Filter'))
            ->emptyStateHeading('Belum ada armada yang ditambahkan')
            ->emptyStateDescription('Silakan tambahkan armada terlebih dahulu untuk mulai melakukan pengelolaan data.')
            ->recordActions([
                // ViewAction::make(),
                EditAction::make()
                    ->iconButton('heroicon-s-pencil-square')
                    ->size('lg')
                    ->tooltip('Ubah')
                    ->modalHeading('Ubah Armada')
                    ->modalWidth('xl')
                    ->stickyModalHeader()
                    ->stickyModalFooter()
                    ->modalSubmitActionLabel('Simpan Perubahan')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Data armada berhasil diperbarui'),
                DeleteAction::make()
                    ->iconButton('heroicon-s-trash')
                    ->color('danger')
                    ->size('lg')
                    ->tooltip(fn ($record) => 
                        $record->status === 'disewa'
                            ? 'Tidak bisa dihapus karena sedang disewa'
                            : 'Hapus'
                    )
                    ->before(function ($record, $action) {

                        if ($record->status === 'disewa') {
                
                            Notification::make()
                                ->title('Tidak dapat menghapus armada')
                                ->body('Armada ini sedang disewa. Selesaikan atau batalkan penyewaan terlebih dahulu.')
                                ->danger()
                                ->duration(5000)
                                ->send();

                            $action->cancel();
                        }
                    })
                    ->modalHeading('Hapus Data Armada?')
                    ->modalDescription('Apakah Anda yakin ingin menghapus data armada ini? Data yang telah dihapus tidak dapat dikembalikan.')
                    ->modalSubmitActionLabel('Ya, Hapus')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Armada telah dihapus'),
                RestoreAction::make()
                    ->iconButton('heroicon-s-arrow-path')
                    ->size('lg')
                    ->tooltip('Pulihkan')
                    ->modalHeading('Pulihkan Data Armada?')
                    ->modalDescription('Data armada yang dipulihkan akan kembali muncul pada daftar armada.')
                    ->modalSubmitActionLabel('Ya, Pulihkan')
                    ->modalCancelActionLabel('Batal')
                    ->successNotificationTitle('Armada berhasil dipulihkan'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->action(function ($records) {

                            $deleted = 0;
                            $blocked = 0;
                    
                            foreach ($records as $record) {
                    
                                if ($record->status === 'disewa') {
                                    $blocked++;
                                    continue;
                                }
                    
                                $record->delete();
                                $deleted++;
                            }
                    
                            if ($deleted > 0) {
                                Notification::make()
                                    ->title('Berhasil menghapus armada')
                                    ->body("{$deleted} armada berhasil dihapus.")
                                    ->success()
                                    ->send();
                            }
                    
                            if ($blocked > 0) {
                                Notification::make()
                                    ->title('Sebagian armada tidak dapat dihapus')
                                    ->body("{$blocked} armada sedang disewa.")
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->modalHeading('Hapus Beberapa Data Armada?')
                        ->modalDescription('Apakah Anda yakin ingin menghapus data armada yang dipilih?')
                        ->modalSubmitActionLabel('Ya, Hapus')
                        ->modalCancelActionLabel('Batal')
                        ->successNotification(null),
                        // ->successNotificationTitle('Data armada berhasil dihapus'),
                    RestoreBulkAction::make()
                        ->label('Pulihkan Terpilih'),
                ])
            ->label('Aksi')
            ]);
    }
}
