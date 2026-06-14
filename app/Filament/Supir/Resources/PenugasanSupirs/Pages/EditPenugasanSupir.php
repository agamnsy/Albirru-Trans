<?php

namespace App\Filament\Supir\Resources\PenugasanSupirs\Pages;

use App\Filament\Supir\Resources\PenugasanSupirs\PenugasanSupirResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPenugasanSupir extends EditRecord
{
    protected static string $resource = PenugasanSupirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
