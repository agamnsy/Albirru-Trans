<?php

namespace App\Filament\Resources\Armadas\Schemas;

use App\Rules\NoEmoji;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class ArmadaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('foto')
                    ->label('Foto/Video Armada')
                    ->multiple()
                    ->maxFiles(5)
                    ->directory('armada')
                    ->columnSpanFull()
                    ->panelLayout('grid')
                    ->reorderable()
                    ->appendFiles(false)
                    ->acceptedFileTypes(['image/*', 'video/mp4', 'video/webm', 'video/quicktime'])
                    ->validationMessages([
                        'maxFiles' => 'Foto/Video Armada tidak boleh lebih dari 5 foto.',
                        'required' => 'Mohon unggah foto/video armada terlebih dahulu',
                    ])
                    ->required(),
                TextInput::make('nama_bus')
                    ->label('Nama Bus')
                    ->placeholder('Masukkan nama bus')
                    ->validationMessages([
                        'required' => 'Nama bus wajib diisi',
                    ])
                    ->rules([
                        new NoEmoji(),
                    ])
                    ->required(),
                TextInput::make('kapasitas')
                    ->label('Kapasitas Bus')
                    ->placeholder('Masukkan kapasitas bus')
                    ->required()
                    ->validationMessages([
                        'required' => 'Kapasitas bus wajib diisi',
                    ])
                    ->numeric(),
                Textarea::make('deskripsi')
                    ->label('Deskripsi Bus')
                    ->placeholder('Masukkan deskripsi bus')
                    ->rows(3)
                    ->columnSpanFull()
                    ->validationMessages([
                        'required' => 'Deskripsi bus wajib diisi',
                    ])
                    ->rules([
                        new NoEmoji(),
                    ])
                    ->required(),
                Select::make('status')
                    ->label('Status Unit')
                    ->options([
                        'tersedia' => 'Tersedia',
                        'maintenance' => 'Maintenance',
                    ])
                    ->default('tersedia')
                    ->columnSpanFull()
                    ->native(false)
                    ->selectablePlaceholder(false)
                    ->rules(function ($record) {
                        return [
                            function (string $attribute, $value, \Closure $fail) use ($record) {
                                if (! $record) {
                                    return;
                                }
                
                                if ($value !== 'maintenance') {
                                    return;
                                }
                
                                $hasActiveBooking = $record->penyewaans()
                                    ->whereIn('status', ['pending', 'dikonfirmasi', 'berjalan'])
                                    ->exists();
                
                                if ($hasActiveBooking) {
                                    $fail('Armada ini masih memiliki penyewaan aktif atau terjadwal. Silakan ubah armada pada data penyewaan terlebih dahulu sebelum mengubah status unit menjadi maintenance.');
                                }
                            },
                        ];
                    })
                    ->validationMessages([
                        'required' => 'Status bus wajib dipilih',
                    ])
                    ->required(),
            ]);
    }
}
