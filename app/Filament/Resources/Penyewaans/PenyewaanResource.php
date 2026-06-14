<?php

namespace App\Filament\Resources\Penyewaans;

use App\Filament\Resources\Penyewaans\RelationManagers\AktivitasPerjalanansRelationManager;
use App\Filament\Resources\Penyewaans\RelationManagers\PenugasanSupirsRelationManager;

use App\Models\Penyewaan;
use App\Models\PenugasanSupir;

use App\Filament\Resources\Penyewaans\Pages\CreatePenyewaan;
use App\Filament\Resources\Penyewaans\Pages\EditPenyewaan;
use App\Filament\Resources\Penyewaans\Pages\ListPenyewaans;
use App\Filament\Resources\Penyewaans\Pages\ViewPenyewaan;
use App\Filament\Resources\Penyewaans\Schemas\PenyewaanForm;
use App\Filament\Resources\Penyewaans\Schemas\PenyewaanInfolist;
use App\Filament\Resources\Penyewaans\Tables\PenyewaansTable;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PenyewaanResource extends Resource
{
    protected static ?string $model = Penyewaan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string | BackedEnum | null $activeNavigationIcon = 'heroicon-s-calendar-days';

    protected static string | UnitEnum | null $navigationGroup = 'Operasional';

    protected static ?string $recordTitleAttribute = 'Penyewaan';

    protected static ?string $navigationLabel = 'Penyewaan';

    protected static ?string $pluralModelLabel = 'Daftar Penyewaan';

    protected static ?string $modelLabel = 'Penyewaan';

    public static function form(Schema $schema): Schema
    {
        return PenyewaanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PenyewaanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenyewaansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AktivitasPerjalanansRelationManager::class,
            PenugasanSupirsRelationManager::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $penyewaanPending = Penyewaan::query()
            ->where('status', 'pending')
            ->count();

        $penugasanDitolak = PenugasanSupir::query()
            ->where('status', 'ditolak')
            ->whereHas('penyewaan', function ($query) {
                $query->whereIn('status', ['pending', 'dikonfirmasi']);
            })
            ->count();

        $total = $penyewaanPending + $penugasanDitolak;

        return $total > 0 ? (string) $total : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPenyewaans::route('/'),
            // 'create' => CreatePenyewaan::route('/create'),
            'view' => ViewPenyewaan::route('/{record}'),
            // 'edit' => EditPenyewaan::route('/{record}/edit'),
        ];
    }
}
