<?php

namespace App\Filament\Resources\Penyewaans\Pages;

use App\Filament\Resources\Penyewaans\PenyewaanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;


class ListPenyewaans extends ListRecords
{
    protected static string $resource = PenyewaanResource::class;

    protected static ?string $title = 'Daftar Penyewaan';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Penyewaan')
                ->modalHeading('Tambah Penyewaan Baru')
                ->modalSubmitActionLabel('Tambah Penyewaan')
                ->createAnother(false)
                ->modalCancelActionLabel('Batal')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Penyewaan Ditambahkan')
                        ->body('Berhasil menambahkan penyewaan baru')
                )
                ->stickyModalHeader()
                ->stickyModalFooter()
                // Penting: Pindahkan logika update status armada ke sini jika di Model belum ada
                ->after(function ($record) {
                    $record->armada->update(['status' => 'disewa']);
                }),
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
