<?php

namespace App\Filament\Resources\Penyewaans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class PenyewaanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Informasi Penyewaan')
                    ->description('Ringkasan data penyewaan pelanggan.')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('pelanggan.nama')
                            ->label('Nama Pelanggan')
                            ->icon('heroicon-s-user')
                            ->iconColor('primary')
                            ->placeholder('-'),

                        TextEntry::make('pelanggan.no_hp')
                            ->label('No HP')
                            ->icon('heroicon-s-phone')
                            ->iconColor('primary')
                            ->placeholder('-'),

                        TextEntry::make('armada.nama_bus')
                            ->label('Armada')
                            ->icon('heroicon-s-key')
                            ->iconColor('primary')
                            ->placeholder('-'),

                        TextEntry::make('tanggal_mulai')
                            ->label('Mulai Sewa')
                            ->icon('heroicon-s-calendar-days')
                            ->iconColor('primary')
                            ->formatStateUsing(function ($record) {
                                $tanggal = $record->tanggal_mulai
                                    ? $record->tanggal_mulai->translatedFormat('d F Y')
                                    : '-';
                        
                                $jam = $record->jam_mulai
                                    ? Carbon::parse($record->jam_mulai)->format('H:i') . ' WIB'
                                    : '-';
                        
                                return "{$tanggal}, {$jam}";
                            }),

                        TextEntry::make('tanggal_selesai')
                            ->label('Selesai Sewa')
                            ->icon('heroicon-s-calendar-days')
                            ->iconColor('primary')
                            ->formatStateUsing(function ($record) {
                                $tanggal = $record->tanggal_selesai
                                    ? $record->tanggal_selesai->translatedFormat('d F Y')
                                    : '-';
                        
                                $jam = $record->jam_selesai
                                    ? Carbon::parse($record->jam_selesai)->format('H:i') . ' WIB'
                                    : '-';
                        
                                return "{$tanggal}, {$jam}";
                            }),

                        TextEntry::make('status')
                            ->label('Status Penyewaan')
                            ->badge()
                            ->size('xl')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'pending' => 'Pending',
                                'dikonfirmasi' => 'Dikonfirmasi',
                                'berjalan' => 'Berjalan',
                                'selesai' => 'Selesai',
                                'dibatalkan' => 'Dibatalkan',
                                default => ucfirst($state),
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'dikonfirmasi' => 'success',
                                'berjalan' => 'success',
                                'selesai' => 'primary',
                                'dibatalkan' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('alamat_penjemputan')
                            ->label('Alamat Penjemputan')
                            ->icon('heroicon-s-map-pin')
                            ->iconColor('primary')
                            ->placeholder('-'),
                            // ->columnSpanFull(),

                        TextEntry::make('tujuan')
                            ->label('Tujuan Destinasi')
                            ->icon('heroicon-s-map')
                            ->iconColor('primary')
                            ->placeholder('-'),
                            // ->columnSpanFull(),
                    ]),

                Section::make('Informasi Sistem')
                    ->description('Waktu pencatatan dan perubahan data penyewaan.')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Dibuat Pada')
                            ->formatStateUsing(fn ($state) => $state
                                ? Carbon::parse($state)->translatedFormat('d F Y, H:i') . ' WIB'
                                : '-'
                            )
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Terakhir Diubah')
                            ->formatStateUsing(fn ($state) => $state
                                ? Carbon::parse($state)->translatedFormat('d F Y, H:i') . ' WIB'
                                : '-'
                            )
                            ->placeholder('-'),
                    ]),
            ]);
    }
}