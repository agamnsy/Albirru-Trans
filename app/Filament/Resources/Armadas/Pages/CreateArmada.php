<?php

namespace App\Filament\Resources\Armadas\Pages;

use App\Filament\Resources\Armadas\ArmadaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArmada extends CreateRecord
{
    protected static string $resource = ArmadaResource::class; 

    protected static ?string $title = 'Tambah Armada';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }


    public function getBreadcrumb(): string
    {
        return 'Tambah Armada';
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Armada berhasil ditambahkan';
    }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Tambah Armada');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Batal');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->hidden();
    }
}
