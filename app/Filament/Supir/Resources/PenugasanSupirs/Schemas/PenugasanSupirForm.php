<?php

namespace App\Filament\Supir\Resources\PenugasanSupirs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PenugasanSupirForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // TextInput::make('penyewaan_id')
                //     ->required(),
                // TextInput::make('supir_id')
                //     ->required()
                //     ->numeric(),
                // Select::make('status')
                //     ->options(['ditugaskan' => 'Ditugaskan', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak'])
                //     ->default('ditugaskan')
                //     ->required(),
                // Textarea::make('alasan_penolakan')
                //     ->columnSpanFull(),
                // DateTimePicker::make('assigned_at'),
                // DateTimePicker::make('responded_at'),
            ]);
    }
}
