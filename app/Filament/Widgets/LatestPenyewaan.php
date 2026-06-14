<?php

namespace App\Filament\Widgets;

use Filament\Tables;
// use Filament\Actions\BulkActionGroup;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
// use Penyewaan;
use App\Models\Penyewaan;
use App\Filament\Resources\PenyewaanResource;

class LatestPenyewaan extends TableWidget
{
    // Mengatur urutan (setelah StatsOverview)
    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    // Membuat tabel memakan lebar penuh layar dashboard
    protected string|int|array $columnSpan = 'full';

    // Judul Widget
    protected static ?string $heading = 'Penyewaan Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Penyewaan::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('pelanggan.nama')
                    ->label('Nama Pelanggan'),
                Tables\Columns\TextColumn::make('armada.nama_bus')
                    ->label('Armada yang Disewa'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status Penyewaan')
                    ->size('xl')
                    // ->size(Tables\Columns\TextColumn\TextColumnSize::large())
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'dikonfirmasi' => 'success',
                        'berjalan' => 'success',
                        'selesai' => 'info',
                        'dibatalkan' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Pemesanan')
                    ->dateTime('d M Y')
                    ->description(fn ($record) => $record->created_at->format('H:i')),
            ])
            ->paginated(false)
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                // ViewAction::make(),
            ]);
            // ->toolbarActions([
            //     BulkActionGroup::make([
            //         //
            //     ]),
            // ]);
    }
}
