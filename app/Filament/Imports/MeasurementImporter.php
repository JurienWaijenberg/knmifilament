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
                ->guess(['Location', 'location'])
                ->rules(['nullable', 'string', 'max:255'])
                ->fillRecordUsing(function (Measurement $record, ?string $state): void {
                    // Don't set location directly - we'll set location_id in resolveRecord() and afterFill()
                    // This prevents Filament from trying to set $record->location
                })
                ->example('Central Station'),
            ImportColumn::make('tube_id')
                ->label('Tube ID')
                ->guess(['Tube ID', 'tube_id', 'Tube ID'])
                ->rules(['required', 'string', 'max:255'])
                ->example('TUBE-001'),
            ImportColumn::make('month')
                ->guess(['Month', 'month'])
                ->castStateUsing(function (string $state): ?int {
                    if (blank($state)) {
                        return null;
                    }

                    // Convert Dutch month names to numbers
                    $months = [
                        'januari' => 1, 'jan' => 1,
                        'februari' => 2, 'feb' => 2,
                        'maart' => 3, 'mrt' => 3,
                        'april' => 4, 'apr' => 4,
                        'mei' => 5, 'may' => 5,
                        'juni' => 6, 'jun' => 6, 'june' => 6,
                        'juli' => 7, 'jul' => 7, 'july' => 7,
                        'augustus' => 8, 'aug' => 8, 'august' => 8,
                        'september' => 9, 'sep' => 9, 'sept' => 9,
                        'oktober' => 10, 'okt' => 10, 'oct' => 10, 'october' => 10,
                        'november' => 11, 'nov' => 11,
                        'december' => 12, 'dec' => 12,
                    ];

                    $state = strtolower(trim($state));

                    if (isset($months[$state])) {
                        return $months[$state];
                    }

                    // If it's already a number, return it
                    return (int) $state;
                })
                ->rules(['required', 'integer', 'min:1', 'max:12'])
                ->example('1'),
            ImportColumn::make('year')
                ->guess(['Year', 'year'])
                ->numeric()
                ->rules(['required', 'integer', 'min:1900', 'max:2100'])
                ->example('2024'),
            ImportColumn::make('start_date')
                ->guess(['start date', 'Start Date', 'start_date', 'StartDateTime'])
                ->castStateUsing(function (string $state): ?string {
                    if (blank($state)) {
                        return null;
                    }

                    // Extract date from datetime string if needed (e.g., "1-05-25 7:21" -> "1-05-25")
                    if (str_contains($state, ' ')) {
                        $parts = explode(' ', $state);
                        $state = $parts[0];
                    }

                    // Convert d-m-y format to Y-m-d (e.g., "1-05-25" -> "2025-05-01")
                    if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{2})$/', $state, $matches)) {
                        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                        $year = '20'.$matches[3]; // Assume 20xx
                        $state = $year.'-'.$month.'-'.$day;
                    }

                    return $state;
                })
                ->rules(['required', 'date'])
                ->example('2024-01-15'),
            ImportColumn::make('start_time')
                ->guess(['start time', 'Start Time', 'start_time', 'StartDateTime'])
                ->castStateUsing(function (string $state): ?string {
                    if (blank($state)) {
                        return null;
                    }

                    // Extract time from datetime string if needed (e.g., "1-05-25 7:21" -> "07:21")
                    if (str_contains($state, ' ')) {
                        $parts = explode(' ', $state);
                        $state = end($parts);
                    }

                    // Remove seconds if present (e.g., "10:00:00" -> "10:00")
                    if (substr_count($state, ':') === 2) {
                        $state = substr($state, 0, 5);
                    }

                    // Ensure format is H:i (pad with zero if needed, e.g., "7:21" -> "07:21")
                    $parts = explode(':', $state);
                    if (count($parts) === 2) {
                        $state = str_pad($parts[0], 2, '0', STR_PAD_LEFT).':'.str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                    }

                    return $state;
                })
                ->rules(['required', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'])
                ->example('08:00'),
            ImportColumn::make('end_date')
                ->guess(['end date', 'End Date', 'end_date', 'EndDateTime'])
                ->castStateUsing(function (string $state): ?string {
                    if (blank($state)) {
                        return null;
                    }

                    // Extract date from datetime string if needed
                    if (str_contains($state, ' ')) {
                        $parts = explode(' ', $state);
                        $state = $parts[0];
                    }

                    // Convert d-m-y format to Y-m-d (e.g., "30-05-25" -> "2025-05-30")
                    if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{2})$/', $state, $matches)) {
                        $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                        $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                        $year = '20'.$matches[3]; // Assume 20xx
                        $state = $year.'-'.$month.'-'.$day;
                    }

                    return $state;
                })
                ->rules(['required', 'date'])
                ->example('2024-01-15'),
            ImportColumn::make('end_time')
                ->guess(['end time', 'End Time', 'end_time', 'EndDateTime'])
                ->castStateUsing(function (string $state): ?string {
                    if (blank($state)) {
                        return null;
                    }

                    // Extract time from datetime string if needed
                    if (str_contains($state, ' ')) {
                        $parts = explode(' ', $state);
                        $state = end($parts);
                    }

                    // Remove seconds if present
                    if (substr_count($state, ':') === 2) {
                        $state = substr($state, 0, 5);
                    }

                    // Ensure format is H:i (pad with zero if needed)
                    $parts = explode(':', $state);
                    if (count($parts) === 2) {
                        $state = str_pad($parts[0], 2, '0', STR_PAD_LEFT).':'.str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                    }

                    return $state;
                })
                ->rules(['required', 'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'])
                ->example('20:00'),
            ImportColumn::make('no2_concentration')
                ->label('NO2 Concentration')
                ->guess(['NO2 concentration', 'NO2 Concentration', 'no2_concentration'])
                ->castStateUsing(function (string $state): ?float {
                    if (blank($state)) {
                        return null;
                    }

                    // Convert comma to dot for decimal separator (European format)
                    $state = str_replace(',', '.', $state);

                    return (float) $state;
                })
                ->rules(['required', 'numeric', 'min:0'])
                ->example('0.0125'),
            ImportColumn::make('remarks')
                ->guess(['Remarks', 'remarks'])
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

        // Check if measurement already exists - if so, skip (return null)
        // Use null coalescing to prevent undefined array key errors
        $existing = Measurement::where('location_id', $locationId)
            ->where('tube_id', $this->data['tube_id'] ?? null)
            ->where('month', $this->data['month'] ?? null)
            ->where('year', $this->data['year'] ?? null)
            ->where('start_date', $this->data['start_date'] ?? null)
            ->first();

        if ($existing) {
            return null; // Skip duplicate
        }

        // Create new measurement with location_id set immediately
        $measurement = new Measurement;
        $measurement->location_id = $locationId;

        return $measurement;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your measurement import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
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
