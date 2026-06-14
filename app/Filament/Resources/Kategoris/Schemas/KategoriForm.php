<?php

namespace App\Filament\Resources\Kategoris\Schemas;

use App\Rules\NoEmoji;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class KategoriForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Kategori')
                    ->placeholder('Masukkan nama kategori')
                    ->columnSpanFull()
                    ->validationMessages([
                        'required' => 'Nama kategori wajib diisi',
                    ])
                    ->rules([
                        new NoEmoji(),
                    ])
                    ->required(),
                Select::make('warna')
                    ->label('Warna Kategori')
                    ->options([
                        'primary' => 'Biru',
                        'success' => 'Hijau',
                        'danger' => 'Merah',
                        'warning' => 'Kuning',
                        'gray' => 'Abu-abu',
                    ])
                    ->native(false)
                    ->columnSpanFull()
                    ->default('primary')
                    ->validationMessages([
                        'required' => 'Pilih warna kategori terlebih dahulu',
                    ])
                    ->required(),
            ]);
    }
}
