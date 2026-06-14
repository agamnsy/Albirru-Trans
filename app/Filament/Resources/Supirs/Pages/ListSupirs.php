<?php

namespace App\Filament\Resources\Supirs\Pages;

use App\Filament\Resources\Supirs\SupirResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListSupirs extends ListRecords
{
    protected static string $resource = SupirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Supir')
                ->modalHeading('Tambah Supir Baru')
                ->modalSubmitActionLabel('Tambah Supir')
                ->modalWidth('md')
                ->createAnother(false)
                ->modalCancelActionLabel('Batal')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Supir Ditambahkan')
                        ->body('Berhasil menambahkan data supir baru')
                )
                ->stickyModalHeader()
                ->stickyModalFooter(),
        ];
    }

    public function getBreadcrumb(): string
    {
        return '';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
