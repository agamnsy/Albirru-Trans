<?php

namespace App\Filament\Resources\Penyewaans\Pages;

use App\Filament\Resources\Penyewaans\PenyewaanResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewPenyewaan extends ViewRecord
{
    protected static string $resource = PenyewaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('kembali')
                ->label('Kembali ke Daftar Penyewaan')
                // ->icon('heroicon-o-arrow-left')
                ->color('primary')
                ->url(PenyewaanResource::getUrl('index')),
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Penyewaan';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
