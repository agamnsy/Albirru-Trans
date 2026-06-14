<?php

namespace App\Filament\Resources\Armadas\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ArmadaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('foto')
                    ->image()
                    ->columnSpanFull(),
                TextEntry::make('nama_bus'),
                TextEntry::make('kapasitas')
                    ->numeric(),
                TextEntry::make('deskripsi')
                    ->columnSpanFull(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
