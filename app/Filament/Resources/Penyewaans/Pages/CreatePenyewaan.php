<?php

namespace App\Filament\Resources\Penyewaans\Pages;

use App\Filament\Resources\Penyewaans\PenyewaanResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
// use Filament\Action\Actions;
use App\Models\Pelanggan;
use App\Models\Armada;

class CreatePenyewaan extends CreateRecord
{
    protected static string $resource = PenyewaanResource::class;

    protected static ?string $title = 'Tambah Penyewaan';

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Tambah Penyewaan');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Batal');
    }

    public function getBreadcrumb(): string
    {
        return 'Tambah Penyewaan';
    }
}
