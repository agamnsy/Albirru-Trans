<?php

namespace App\Filament\Resources\Galeris\Pages;

use App\Filament\Resources\Galeris\GaleriResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListGaleris extends ListRecords
{
    protected static string $resource = GaleriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Galeri')
                ->modalWidth('3xl')
                ->modalHeading('Tambah Galeri Baru')
                ->modalSubmitActionLabel('Tambah ke Galeri')
                ->createAnother(false)
                ->modalCancelActionLabel('Batal')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Galeri Ditambahkan')
                        ->body('Berhasil menambahkan galeri baru')
                )
                ->stickyModalHeader()
                ->stickyModalFooter(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
