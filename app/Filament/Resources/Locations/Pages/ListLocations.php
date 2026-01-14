<?php

namespace App\Filament\Resources\Locations\Pages;

use App\Filament\Imports\LocationImporter;
use App\Filament\Resources\Locations\LocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                ->importer(LocationImporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
