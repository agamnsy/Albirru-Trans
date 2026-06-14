<?php

namespace App\Filament\Resources\Armadas;

use App\Filament\Resources\Armadas\Pages\CreateArmada;
use App\Filament\Resources\Armadas\Pages\EditArmada;
use App\Filament\Resources\Armadas\Pages\ListArmadas;
use App\Filament\Resources\Armadas\Pages\ViewArmada;
use App\Filament\Resources\Armadas\Schemas\ArmadaForm;
use App\Filament\Resources\Armadas\Schemas\ArmadaInfolist;
use App\Filament\Resources\Armadas\Tables\ArmadasTable;
use App\Models\Armada;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ArmadaResource extends Resource
{
    protected static ?string $model = Armada::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static string | BackedEnum | null $activeNavigationIcon = 'heroicon-s-key';

    protected static string | UnitEnum | null $navigationGroup = 'Operasional';

    protected static ?string $recordTitleAttribute = 'Armada';

    protected static ?string $navigationLabel = 'Armada';

    protected static ?string $pluralModelLabel = 'Daftar Armada';
    
    protected static ?string $modelLabel = 'Armada';

    public static function form(Schema $schema): Schema
    {
        return ArmadaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ArmadaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArmadasTable::configure($table);
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
            'index' => ListArmadas::route('/'),
            // 'create' => CreateArmada::route('/create'),
            // 'view' => ViewArmada::route('/{record}'),
            // 'edit' => EditArmada::route('/{record}/edit'),
        ];
    }
}
