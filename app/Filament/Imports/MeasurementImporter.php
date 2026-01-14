<?php

namespace App\Filament\Imports;

use App\Models\Location;
use App\Models\Measurement;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class MeasurementImporter extends Importer
{
    protected static ?string $model = Measurement::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('location')
                ->label('Location Name')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Central Station'),
            ImportColumn::make('tube_id')
                ->label('Tube ID')
                ->rules(['required', 'string', 'max:255'])
                ->example('TUBE-001'),
            ImportColumn::make('month')
                ->numeric()
                ->rules(['required', 'integer', 'min:1', 'max:12'])
                ->example('1'),
            ImportColumn::make('year')
                ->numeric()
                ->rules(['required', 'integer', 'min:1900', 'max:2100'])
                ->example('2024'),
            ImportColumn::make('start_date')
                ->rules(['required', 'date'])
                ->example('2024-01-15'),
            ImportColumn::make('start_time')
                ->rules(['required', 'date_format:H:i'])
                ->example('08:00'),
            ImportColumn::make('end_date')
                ->rules(['required', 'date'])
                ->example('2024-01-15'),
            ImportColumn::make('end_time')
                ->rules(['required', 'date_format:H:i'])
                ->example('20:00'),
            ImportColumn::make('no2_concentration')
                ->label('NO2 Concentration')
                ->numeric()
                ->rules(['required', 'numeric', 'min:0'])
                ->example('0.0125'),
            ImportColumn::make('remarks')
                ->rules(['nullable', 'string'])
                ->example('Clear weather conditions'),
        ];
    }

    public function resolveRecord(): ?Measurement
    {
        // If location_id is provided in options (from relation manager), use it
        if (isset($this->options['location_id'])) {
            $locationId = $this->options['location_id'];
        } else {
            // Otherwise, find location by name (required when not in relation manager)
            if (empty($this->data['location'])) {
                return null;
            }

            $location = Location::where('name', $this->data['location'])->first();

            if (! $location) {
                return null;
            }

            $locationId = $location->id;
        }

        // Try to find existing measurement or create new
        return Measurement::firstOrNew([
            'location_id' => $locationId,
            'tube_id' => $this->data['tube_id'],
            'month' => $this->data['month'],
            'year' => $this->data['year'],
            'start_date' => $this->data['start_date'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your measurement import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    protected function afterFill(): void
    {
        // Set location_id from options (relation manager) or location name lookup
        if (isset($this->options['location_id'])) {
            $this->record->location_id = $this->options['location_id'];
        } else {
            $location = Location::where('name', $this->data['location'])->first();

            if ($location) {
                $this->record->location_id = $location->id;
            }
        }

        // Convert time strings to proper format
        if (isset($this->data['start_time'])) {
            $this->record->start_time = $this->data['start_time'];
        }

        if (isset($this->data['end_time'])) {
            $this->record->end_time = $this->data['end_time'];
        }
    }
}
