<?php

namespace App\Filament\Resources\Supirs\Pages;

use App\Filament\Resources\Supirs\SupirResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSupir extends CreateRecord
{
    protected static string $resource = SupirResource::class;

    protected static ?string $title = 'Tambah Supir';

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Tambah Supir');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()
            ->hidden();
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Batal');
    }
}
