<?php

namespace App\Filament\Resources\Kategoris\Pages;

use App\Filament\Resources\Kategoris\KategoriResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListKategoris extends ListRecords
{
    protected static string $resource = KategoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Kategori') // Mengubah tombol "New Pelanggan"
                ->modalHeading('Tambah Kategori Baru')
                ->modalSubmitActionLabel('Tambah Kategori')
                ->createAnother(false)
                ->modalCancelActionLabel('Batal')
                ->modalWidth('md')
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Kategori Ditambahkan')
                        ->body('Berhasil menambahkan kategori baru')
                ),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
