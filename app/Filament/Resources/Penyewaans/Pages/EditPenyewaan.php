<?php

namespace App\Filament\Resources\Penyewaans\Pages;

use App\Filament\Resources\Penyewaans\PenyewaanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPenyewaan extends EditRecord
{
    protected static string $resource = PenyewaanResource::class;

    protected $oldArmadaId;

    protected function getHeaderActions(): array
    {
        return [
            // ViewAction::make(),
            // DeleteAction::make(),
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

    protected function beforeSave(): void
    {
        $this->oldArmadaId = $this->record->armada_id;
    }

    protected function afterSave(): void
    {
        $newArmadaId = $this->record->armada_id;

        // kalau armada berubah
        if ($this->oldArmadaId != $newArmadaId) {

            // balikin armada lama
            if ($this->oldArmadaId) {
                \App\Models\Armada::where('id', $this->oldArmadaId)
                    ->update(['status' => 'tersedia']);
            }

            // set armada baru
            if ($newArmadaId) {
                \App\Models\Armada::where('id', $newArmadaId)
                    ->update(['status' => 'disewa']);
            }
        }
    }
}
