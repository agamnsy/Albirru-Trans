<?php

namespace App\Filament\Supir\Resources\PenugasanSupirs\Pages;

use App\Filament\Supir\Resources\PenugasanSupirs\PenugasanSupirResource;
use Filament\Resources\Pages\ListRecords;

class ListPenugasanSupirs extends ListRecords
{
    protected static string $resource = PenugasanSupirResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}