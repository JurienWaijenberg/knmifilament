<?php

namespace App\Filament\Resources\Cities;

use App\Filament\Resources\Cities\Pages\CreateCities;
use App\Filament\Resources\Cities\Pages\EditCities;
use App\Filament\Resources\Cities\Pages\ListCities;
use App\Filament\Resources\Cities\Pages\ViewCities;
use App\Filament\Resources\Cities\RelationManagers\LocationsRelationManager;
use App\Filament\Resources\Cities\Schemas\CitiesForm;
use App\Filament\Resources\Cities\Schemas\CitiesInfolist;
use App\Filament\Resources\Cities\Tables\CitiesTable;
use App\Models\Cities;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CitiesResource extends Resource
{
    protected static ?string $model = Cities::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeEuropeAfrica;

    protected static ?string $recordTitleAttribute = 'Cities';

    public static function form(Schema $schema): Schema
    {
        return CitiesForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CitiesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CitiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            LocationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCities::route('/'),
            'create' => CreateCities::route('/create'),
            'view' => ViewCities::route('/{record}'),
            'edit' => EditCities::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
