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
                    ->prefix('+62')
                    ->placeholder('81234567890')
                    ->helperText('Masukkan nomor tanpa angka 0 di depan.')
                    ->columnSpanFull()
                    ->numeric()
                    ->tel()
                    ->maxLength(12)
                    ->formatStateUsing(function ($state) {
                        if (! $state) {
                            return null;
                        }
                
                        $number = preg_replace('/[^0-9]/', '', $state);
                
                        if (str_starts_with($number, '08')) {
                            return substr($number, 1);
                        }
                
                        if (str_starts_with($number, '62')) {
                            return substr($number, 2);
                        }
                
                        return $number;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (! $state) {
                            return null;
                        }
                
                        $number = preg_replace('/[^0-9]/', '', $state);
                
                        if (str_starts_with($number, '08')) {
                            return $number;
                        }
                
                        if (str_starts_with($number, '62')) {
                            return '0' . substr($number, 2);
                        }
                
                        if (str_starts_with($number, '8')) {
                            return '0' . $number;
                        }
                
                        return $number;
                    })
                    ->rules(function ($record) {
                        return [
                            function (string $attribute, $value, \Closure $fail) use ($record) {
                                $number = preg_replace('/[^0-9]/', '', $value);
                
                                if (str_starts_with($number, '08')) {
                                    $normalized = $number;
                                } elseif (str_starts_with($number, '62')) {
                                    $normalized = '0' . substr($number, 2);
                                } elseif (str_starts_with($number, '8')) {
                                    $normalized = '0' . $number;
                                } else {
                                    $fail('Format nomor HP tidak valid. Gunakan nomor Indonesia, contoh: 81234567890.');
                                    return;
                                }
                
                                if (! preg_match('/^08[0-9]{8,11}$/', $normalized)) {
                                    $fail('Format nomor HP tidak valid. Gunakan nomor Indonesia, contoh: 81234567890.');
                                    return;
                                }
                
                                $exists = \App\Models\Pelanggan::where('no_hp', $normalized)
                                    ->when($record, fn ($query) => $query->where('id', '!=', $record->id))
                                    ->exists();
                
                                if ($exists) {
                                    $fail('Nomor HP ini sudah terdaftar di sistem.');
                                }
                            },
                        ];
                    })
                    ->required()
                    ->validationMessages([
                        'required' => 'Nomor HP wajib diisi',
                        'tel' => 'Format nomor HP tidak valid',
                        'maxLength' => 'Nomor HP tidak boleh lebih dari 12 digit setelah +62',
                    ])
            ]);
    }
}
