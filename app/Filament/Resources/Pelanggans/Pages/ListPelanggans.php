<?php

namespace App\Filament\Resources\Pelanggans\Pages;

use App\Filament\Resources\Pelanggans\PelangganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPelanggans extends ListRecords
{
    protected static string $resource = PelangganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Pelanggan') // Mengubah tombol "New Pelanggan"
                ->modalHeading('Tambah Pelanggan Baru')
                ->modalSubmitActionLabel('Tambah Pelanggan')
                ->createAnother(false)
                ->modalCancelActionLabel('Batal')
                ->modalWidth('md')
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->successNotification(
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Pelanggan Ditambahkan')
                        ->body('Berhasil menambahkan pelanggan baru')
                ),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
