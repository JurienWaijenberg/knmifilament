<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Measurement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id',
        'tube_id',
        'month',
        'year',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'no2_concentration',
        'remarks',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'start_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_date' => 'date',
        'end_time' => 'datetime:H:i',
        'no2_concentration' => 'decimal:4',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
