<?php

namespace App\Filament\Resources\Pelanggans;

use App\Filament\Resources\Pelanggans\Pages\CreatePelanggan;
use App\Filament\Resources\Pelanggans\Pages\EditPelanggan;
use App\Filament\Resources\Pelanggans\Pages\ListPelanggans;
use App\Filament\Resources\Pelanggans\Pages\ViewPelanggan;
use App\Filament\Resources\Pelanggans\Schemas\PelangganForm;
use App\Filament\Resources\Pelanggans\Schemas\PelangganInfolist;
use App\Filament\Resources\Pelanggans\Tables\PelanggansTable;
use App\Models\Pelanggan;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string | BackedEnum | null $activeNavigationIcon = 'heroicon-s-user-group';

    protected static string | UnitEnum | null $navigationGroup = 'Operasional';

    protected static ?string $recordTitleAttribute = 'Pelanggan';

    protected static ?string $navigationLabel = 'Pelanggan';

    protected static ?string $pluralModelLabel = 'Daftar Pelanggan';

    protected static ?string $modelLabel = 'Pelanggan';

    public static function form(Schema $schema): Schema
    {
        return PelangganForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PelangganInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PelanggansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPelanggans::route('/'),
            // 'create' => CreatePelanggan::route('/create'),
            // 'view' => ViewPelanggan::route('/{record}'),
            // 'edit' => EditPelanggan::route('/{record}/edit'),
        ];
    }
}
