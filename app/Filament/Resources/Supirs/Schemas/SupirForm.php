<?php

namespace App\Filament\Resources\Supirs\Schemas;

use App\Rules\NoEmoji;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\Facades\Hash;

class SupirForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('role')
                    ->default('supir')
                    ->dehydrated(true),

                TextInput::make('name')
                    ->label('Nama Supir')
                    ->placeholder('Masukkan nama supir')
                    ->required()
                    ->rules([
                        new NoEmoji(),
                    ])
                    ->validationMessages([
                        'required' => 'Nama supir wajib diisi.',
                    ])
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->placeholder('Masukkan email supir')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->rules([
                        'ends_with:@albirrutrans.com',
                    ])
                    ->validationMessages([
                        'required' => 'Email wajib diisi.',
                        'email' => 'Format email tidak valid.',
                        'unique' => 'Email sudah digunakan.',
                        'ends_with' => 'Email supir harus menggunakan @albirrutrans.com.',
                    ])
                    ->maxLength(255),

                TextInput::make('no_hp')
                    ->label('No HP')
                    ->tel()
                    ->placeholder('Cth: 081234567890')
                    ->maxLength(13)
                    ->required(),

                TextInput::make('password')
                    ->label(fn (string $operation): string => $operation === 'create' ? 'Password' : 'Password Baru')
                    ->password()
                    ->revealable()
                    ->placeholder(fn (string $operation): string => $operation === 'create' ? 'Masukkan password' : 'Masukkan password baru')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->maxLength(255),
                
                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password Baru')
                    ->password()
                    ->revealable()
                    ->placeholder('Masukkan ulang password baru')
                    ->visible(fn (string $operation): bool => $operation === 'edit')
                    ->required(fn (string $operation, Get $get): bool => 
                        $operation === 'edit' && filled($get('password'))
                    )
                    ->same('password')
                    ->dehydrated(false),

                    Select::make('status')
                    ->label('Status')
                    ->native(false)
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ])
                    ->default('aktif')
                    ->required()
                    ->helperText('Status bertugas akan berubah otomatis ketika supir menerima tugas.'),
            ])
            ->columns(1);
    }   
}
