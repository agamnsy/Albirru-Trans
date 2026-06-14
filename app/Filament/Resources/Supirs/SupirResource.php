<?php

namespace App\Filament\Resources\Supirs;

use App\Filament\Resources\Supirs\Pages\CreateSupir;
use App\Filament\Resources\Supirs\Pages\EditSupir;
use App\Filament\Resources\Supirs\Pages\ListSupirs;
use App\Filament\Resources\Supirs\Schemas\SupirForm;
use App\Filament\Resources\Supirs\Tables\SupirsTable;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SupirResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string | BackedEnum | null $activeNavigationIcon = 'heroicon-s-identification';

    protected static string | UnitEnum | null $navigationGroup = 'Operasional';

    protected static ?string $recordTitleAttribute = 'Supir';

    protected static ?string $navigationLabel = 'Supir';

    protected static ?string $modelLabel = 'Supir';

    protected static ?string $pluralModelLabel = 'Daftar Supir';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', 'supir');
    }

    public static function form(Schema $schema): Schema
    {
        return SupirForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupirsTable::configure($table);
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
            'index' => ListSupirs::route('/'),
            // 'create' => CreateSupir::route('/create'),
            // 'edit' => EditSupir::route('/{record}/edit'),
        ];
    }
}
