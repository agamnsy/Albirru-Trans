<?php

namespace App\Filament\Resources\Penyewaans\Pages;

use App\Filament\Resources\Penyewaans\PenyewaanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use App\Models\Armada;

class EditPenyewaan extends EditRecord
{
    protected static string $resource = PenyewaanResource::class;

    protected $oldArmadaId;

    protected function getHeaderActions(): array
    {
        return [
            // 
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record->armada) {
            $data['armada_nama'] = $this->record->armada->nama_bus;
        }

        if ($this->record->pelanggan) {
            $data['nama'] = $this->record->pelanggan->nama;
            $data['no_hp'] = $this->record->pelanggan->no_hp;
        }

        return $data;
    }
}
