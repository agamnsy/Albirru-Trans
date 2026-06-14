<?php

namespace App\Filament\Supir\Resources\PenugasanSupirs\Pages;

use App\Filament\Supir\Resources\PenugasanSupirs\PenugasanSupirResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewPenugasanSupir extends ViewRecord
{
    protected static string $resource = PenugasanSupirResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return 'Detail Perjalanan';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('kembali')
                ->label('Kembali ke Tugas Saya')
                // ->icon('heroicon-s-arrow-left')
                ->color('primary')
                ->url(PenugasanSupirResource::getUrl('index')),
        ];
    }
}