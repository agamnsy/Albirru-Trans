<?php

namespace App\Filament\Supir\Resources\PenugasanSupirs;

use App\Filament\Supir\Resources\PenugasanSupirs\Pages\CreatePenugasanSupir;
use App\Filament\Supir\Resources\PenugasanSupirs\Pages\EditPenugasanSupir;
use App\Filament\Supir\Resources\PenugasanSupirs\Pages\ListPenugasanSupirs;
use App\Filament\Supir\Resources\PenugasanSupirs\Schemas\PenugasanSupirForm;
use App\Filament\Supir\Resources\PenugasanSupirs\Tables\PenugasanSupirsTable;
use App\Filament\Supir\Resources\PenugasanSupirs\RelationManagers\AktivitasPerjalanansRelationManager;
use App\Filament\Supir\Resources\PenugasanSupirs\Pages\ViewPenugasanSupir;
use App\Models\PenugasanSupir;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PenugasanSupirResource extends Resource
{
    protected static ?string $model = PenugasanSupir::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string | BackedEnum | null $activeNavigationIcon = 'heroicon-s-clipboard-document-list';

    protected static string | UnitEnum | null $navigationGroup = 'Operasional';

    protected static ?string $navigationLabel = 'Tugas Saya';

    protected static ?string $modelLabel = 'Tugas';

    protected static ?string $pluralModelLabel = 'Tugas Saya';

    protected static ?string $recordTitleAttribute = 'status';

    public static function form(Schema $schema): Schema
    {
        return PenugasanSupirForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenugasanSupirsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AktivitasPerjalanansRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('supir_id', auth()->id());
    }

    public static function getNavigationBadge(): ?string
    {
        $total = static::getModel()::query()
            ->where('supir_id', auth()->id())
            ->where('status', 'ditugaskan')
            ->whereHas('penyewaan', function ($query) {
                $query->where('status', 'dikonfirmasi');
            })
            ->count();

        return $total > 0 ? (string) $total : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPenugasanSupirs::route('/'),
            'view' => ViewPenugasanSupir::route('/{record}'),
            // 'create' => CreatePenugasanSupir::route('/create'),
            // 'edit' => EditPenugasanSupir::route('/{record}/edit'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
    return $schema
        ->columns(1)
        ->components([
            Section::make('Informasi Penyewaan')
                ->description('Ringkasan data penyewaan yang ditugaskan kepada Anda.')
                ->columns(3)
                ->schema([
                    TextEntry::make('penyewaan.pelanggan.nama')
                        ->label('Nama Pelanggan')
                        ->icon('heroicon-s-user')
                        ->iconColor('primary')
                        ->placeholder('-'),

                    TextEntry::make('penyewaan.pelanggan.no_hp')
                        ->label('No HP')
                        ->icon('heroicon-s-phone')
                        ->iconColor('primary')
                        ->placeholder('-'),

                    TextEntry::make('penyewaan.armada.nama_bus')
                        ->label('Armada')
                        ->icon('heroicon-s-key')
                        ->iconColor('primary')
                        ->placeholder('-'),

                    TextEntry::make('penyewaan.tanggal_mulai')
                        ->label('Tanggal Mulai')
                        ->icon('heroicon-s-calendar-days')
                        ->iconColor('primary')
                        ->formatStateUsing(function ($record) {
                            if (! $record->penyewaan?->tanggal_mulai) {
                                return '-';
                            }
                    
                            $tanggal = \Carbon\Carbon::parse($record->penyewaan->tanggal_mulai)
                                ->locale('id')
                                ->translatedFormat('d F Y');
                    
                            $jam = $record->penyewaan?->jam_mulai
                                ? \Carbon\Carbon::parse($record->penyewaan->jam_mulai)->format('H:i') . ' WIB'
                                : '-';
                    
                            return "{$tanggal}, {$jam}";
                        }),

                    TextEntry::make('penyewaan.tanggal_selesai')
                        ->label('Tanggal Selesai')
                        ->icon('heroicon-s-calendar-days')
                        ->iconColor('primary')
                        ->formatStateUsing(function ($record) {
                            if (! $record->penyewaan?->tanggal_selesai) {
                                return '-';
                            }
                    
                            $tanggal = \Carbon\Carbon::parse($record->penyewaan->tanggal_selesai)
                                ->locale('id')
                                ->translatedFormat('d F Y');
                    
                            $jam = $record->penyewaan?->jam_selesai
                                ? \Carbon\Carbon::parse($record->penyewaan->jam_selesai)->format('H:i') . ' WIB'
                                : '-';
                    
                            return "{$tanggal}, {$jam}";
                        }),

                    TextEntry::make('penyewaan.status')
                        ->label('Status Penyewaan')
                        ->badge()
                        ->size('xl')
                        ->formatStateUsing(fn (?string $state): string => match ($state) {
                            'pending' => 'Pending',
                            'dikonfirmasi' => 'Dikonfirmasi',
                            'berjalan' => 'Berjalan',
                            'selesai' => 'Selesai',
                            'dibatalkan' => 'Dibatalkan',
                            default => '-',
                        })
                        ->color(fn (?string $state): string => match ($state) {
                            'pending' => 'warning',
                            'dikonfirmasi' => 'success',
                            'berjalan' => 'success',
                            'selesai' => 'primary',
                            'dibatalkan' => 'danger',
                            default => 'gray',
                        }),

                    TextEntry::make('penyewaan.alamat_penjemputan')
                        ->label('Alamat Penjemputan')
                        ->icon('heroicon-s-map-pin')
                        ->iconColor('primary')
                        ->placeholder('-'),

                    TextEntry::make('penyewaan.tujuan')
                        ->label('Tujuan Destinasi')
                        ->icon('heroicon-s-map')
                        ->iconColor('primary')
                        ->placeholder('-'),
                ]),

            Section::make('Informasi Tugas')
                ->description('Status penugasan Anda pada penyewaan ini.')
                ->columns(2)
                ->schema([
                    TextEntry::make('status')
                        ->label('Status Tugas')
                        ->badge()
                        ->size('xl')
                        ->formatStateUsing(fn (?string $state): string => match ($state) {
                            'ditugaskan' => 'Ditugaskan',
                            'diterima' => 'Diterima',
                            'ditolak' => 'Ditolak',
                            'dibatalkan' => 'Dibatalkan',
                            default => '-',
                        })
                        ->color(fn (?string $state): string => match ($state) {
                            'ditugaskan' => 'warning',
                            'diterima' => 'success',
                            'ditolak' => 'danger',
                            'dibatalkan' => 'gray',
                            default => 'gray',
                        }),

                    TextEntry::make('responded_at')
                        ->label('Waktu Respons')
                        ->dateTime('d F Y, H:i')
                        ->placeholder('-'),

                    TextEntry::make('alasan_penolakan')
                        ->label('Alasan Penolakan')
                        ->placeholder('-')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
