<?php

namespace App\Filament\Resources\Armadas\Pages;

use App\Filament\Resources\Armadas\ArmadaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewArmada extends ViewRecord
{
    protected static string $resource = ArmadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
