<?php

namespace App\Filament\Resources\Penyewaans\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AktivitasPerjalanansRelationManager extends RelationManager
{
    protected static string $relationship = 'aktivitasPerjalanans';

    protected static ?string $title = 'Aktivitas Perjalanan';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                TextEntry::make('supir.name')
                    ->label('Nama Supir'),

                TextEntry::make('created_at')
                    ->label('Waktu Aktivitas')
                    ->formatStateUsing(fn ($state) => $state
                        ? $state->format('d F Y, H:i')
                        : '-'
                    ),

                TextEntry::make('status')
                    ->label('Status Aktivitas Terakhir')
                    ->badge()
                    ->size('xl')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sampai_penjemputan' => 'Sampai Penjemputan',
                        'mulai_perjalanan' => 'Mulai Perjalanan',
                        'sampai_tujuan' => 'Sampai Tujuan',
                        'perjalanan_pulang' => 'Perjalanan Pulang',
                        'sampai_garasi' => 'Sampai Garasi',
                        'selesai' => 'Selesai',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'sampai_penjemputan' => 'warning',
                        'mulai_perjalanan' => 'success',
                        'sampai_tujuan' => 'success',
                        'perjalanan_pulang' => 'success',
                        'sampai_garasi' => 'success',
                        'selesai' => 'primary',
                        default => 'gray',
                    }),

                TextEntry::make('catatan')
                    ->label('Catatan')
                    ->placeholder('-')
                    ->columnSpanFull(),

                ImageEntry::make('foto')
                    ->label('Foto Dokumentasi')
                    ->disk('public')
                    ->visibility('public')
                    ->height(320)
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('status')
            ->searchable(false)
            ->paginated(false)
            ->columns([
                TextColumn::make('supir.name')
                    ->label('Nama Supir'),

                TextColumn::make('status')
                    ->label('Status Aktivitas')
                    ->badge()
                    ->size('xl')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'sampai_penjemputan' => 'Sampai Penjemputan',
                        'mulai_perjalanan' => 'Mulai Perjalanan',
                        'sampai_tujuan' => 'Sampai Tujuan',
                        'perjalanan_pulang' => 'Perjalanan Pulang',
                        'sampai_garasi' => 'Sampai Garasi',
                        'selesai' => 'Selesai',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'sampai_penjemputan' => 'warning',
                        'mulai_perjalanan' => 'success',
                        'sampai_tujuan' => 'success',
                        'perjalanan_pulang' => 'success',
                        'sampai_garasi' => 'primary',
                        'selesai' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('catatan')
                    ->label('Catatan')
                    ->limit(40)
                    ->placeholder('Tidak ada catatan')
                    ->wrap(),

                ImageColumn::make('foto')
                    ->label('Foto Dokumentasi')
                    ->disk('public')
                    ->stacked()
                    ->limit(5)
                    ->circular()
                    ->placeholder('Tidak ada foto dokumentasi')
                    ->size(48),

                TextColumn::make('created_at')
                    ->label('Waktu Aktivitas')
                    ->dateTime('d F Y')
                    ->description(fn ($record) => $record->created_at?->format('H:i')),
                    // ->sortable(),
                
                TextColumn::make('updated_at')
                    ->label('Update Pada')
                    ->dateTime('d F Y')
                    ->description(fn ($record) => $record->updated_at?->format('H:i')),

            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Belum ada aktivitas perjalanan')
            ->emptyStateDescription('Aktivitas perjalanan akan muncul setelah supir mulai memperbarui progres perjalanan.')
            ->emptyStateIcon('heroicon-o-map-pin')
            ->filters([])
            ->headerActions([])
            ->recordActions([
                ViewAction::make()
                    ->iconButton()
                    ->tooltip('Lihat Detail')
                    ->modalHeading('Detail Aktivitas Perjalanan')
                    ->stickyModalHeader()
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
            ])
            ->toolbarActions([]);
    }
}