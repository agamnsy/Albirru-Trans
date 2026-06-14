<?php

namespace App\Filament\Resources\Supirs\Pages;

use App\Filament\Resources\Supirs\SupirResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSupir extends EditRecord
{
    protected static string $resource = SupirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
