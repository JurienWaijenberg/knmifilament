<?php

namespace App\Filament\Imports;

use App\Models\Cities;
use App\Models\Location;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class LocationImporter extends Importer
{
    protected static ?string $model = Location::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('city')
                ->label('City Name')
                ->rules(['required', 'string', 'max:255'])
                ->example('Amsterdam'),
            ImportColumn::make('name')
                ->rules(['required', 'string', 'max:255'])
                ->example('Central Station'),
            ImportColumn::make('latitude')
                ->castStateUsing(function (string $state): ?float {
                    if (blank($state)) {
                        return null;
                    }

                    // Convert comma to dot for decimal separator (European format)
                    $state = str_replace(',', '.', $state);

                    return (float) $state;
                })
                ->rules(['required', 'numeric', 'between:-90,90'])
                ->example('52.3676'),
            ImportColumn::make('longitude')
                ->castStateUsing(function (string $state): ?float {
                    if (blank($state)) {
                        return null;
                    }

                    // Convert comma to dot for decimal separator (European format)
                    $state = str_replace(',', '.', $state);

                    return (float) $state;
                })
                ->rules(['required', 'numeric', 'between:-180,180'])
                ->example('4.9041'),
            ImportColumn::make('description')
                ->rules(['nullable', 'string'])
                ->example('Main measurement location'),
        ];
    }

    public function resolveRecord(): ?Location
    {
        // Find city
        $city = Cities::where('name', $this->data['city'])->first();

        if (! $city) {
            return null;
        }

        // Check if location already exists - if so, skip (return null)
        $existing = Location::where('name', $this->data['name'])
            ->where('city_id', $city->id)
            ->first();

        if ($existing) {
            return null; // Skip duplicate
        }

        // Create new location with city_id set immediately
        $location = new Location;
        $location->city_id = $city->id;

        return $location;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your location import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }

    protected function afterFill(): void
    {
        // city_id is already set in resolveRecord(), but ensure it's set here too as fallback
        if (! $this->record->city_id) {
            $city = Cities::where('name', $this->data['city'])->first();

            if ($city) {
                $this->record->city_id = $city->id;
            }
        }
    }
}
