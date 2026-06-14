<?php

namespace App\Filament\Resources\Pelanggans\Schemas;

use App\Rules\NoEmoji;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PelangganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Pelanggan')
                    ->placeholder('Masukkan nama pelanggan')
                    ->columnSpanFull()
                    ->validationMessages([
                        'required' => 'Nama pelanggan wajib diisi',
                    ])
                    ->rules([
                        new NoEmoji(),
                    ])
                    ->required(),
                TextInput::make('no_hp')
                    ->label('Nomor HP')
                    ->placeholder('Masukkan nomor HP pelanggan')
                    ->columnSpanFull()
                    ->numeric()
                    ->tel()
                    ->maxLength(13)
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->validationMessages([
                        'unique' => 'Nomor HP ini sudah terdaftar di sistem',
                        'required' => 'Nomor HP wajib diisi',
                        'tel' => 'Format nomor HP tidak valid',
                        'maxLength' => 'Nomor HP tidak boleh lebih dari 13 digit',
                    ])
            ]);
    }
}
