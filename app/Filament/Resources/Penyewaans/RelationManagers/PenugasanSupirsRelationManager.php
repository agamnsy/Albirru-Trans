<?php

namespace App\Filament\Resources\Penyewaans\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;

class PenugasanSupirsRelationManager extends RelationManager
{
    protected static string $relationship = 'penugasanSupirs';

    protected static ?string $title = 'Riwayat Penugasan Supir';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('supir.name')
                    ->label('Nama Supir'),

                TextEntry::make('status')
                    ->label('Status Penugasan')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ditugaskan' => 'Ditugaskan',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                        'dibatalkan' => 'Dibatalkan',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'ditugaskan' => 'warning',
                        'diterima' => 'success',
                        'ditolak' => 'danger',
                        'dibatalkan' => 'gray',
                        default => 'gray',
                    }),

                TextEntry::make('alasan_penolakan')
                    ->label('Alasan Penolakan')
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('assigned_at')
                    ->label('Ditugaskan Pada')
                    ->dateTime('d F Y')
                    ->description(fn ($record) => $record->assigned_at?->format('H:i'))
                    ->placeholder('-'),

                TextEntry::make('responded_at')
                    ->label('Direspons Pada')
                    ->dateTime('d F Y')
                    ->description(fn ($record) => $record->responded_at?->format('H:i'))
                    ->placeholder('-'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status')
            ->searchable(false)
            ->columns([
                TextColumn::make('supir.name')
                    ->label('Nama Supir'),
                    // ->searchable(),

                TextColumn::make('status')
                    ->label('Status Penugasan')
                    ->badge()
                    ->size('xl')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ditugaskan' => 'Ditugaskan',
                        'diterima' => 'Diterima',
                        'ditolak' => 'Ditolak',
                        'dibatalkan' => 'Dibatalkan',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'ditugaskan' => 'warning',
                        'diterima' => 'success',
                        'ditolak' => 'danger',
                        'dibatalkan' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('alasan_penolakan')
                    ->label('Alasan Penolakan')
                    ->limit(40)
                    ->placeholder('Tidak ada'),

                TextColumn::make('assigned_at')
                    ->label('Ditugaskan Pada')
                    ->dateTime('d F Y')
                    ->description(fn ($record) => $record->assigned_at?->format('H:i')),
                    // ->sortable(),

                TextColumn::make('responded_at')
                    ->label('Direspons Pada')
                    ->dateTime('d F Y')
                    ->description(fn ($record) => $record->responded_at?->format('H:i')),
                    // ->sortable(),
            ])
            ->defaultSort('assigned_at', 'desc')
            ->emptyStateHeading('Riwayat penugasan belum tersedia')
            ->emptyStateDescription('Data penugasan supir akan ditampilkan setelah admin menetapkan supir untuk penyewaan ini.')
            ->emptyStateIcon('heroicon-o-identification')
            ->paginated(false)
            ->filters([])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}