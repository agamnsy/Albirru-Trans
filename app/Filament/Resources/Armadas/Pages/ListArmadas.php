<?php

namespace App\Filament\Resources\Armadas\Pages;

use App\Filament\Resources\Armadas\ArmadaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListArmadas extends ListRecords
{
    protected static string $resource = ArmadaResource::class;

    protected static ?string $title = 'Daftar Armada';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Armada')
                ->modalHeading('Tambah Armada Baru')
                ->modalSubmitActionLabel('Tambah Armada')
                ->createAnother(false)
                ->modalCancelActionLabel('Batal')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Armada Ditambahkan')
                        ->body('Berhasil menambahkan armada baru')
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
