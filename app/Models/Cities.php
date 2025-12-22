<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cities extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'country',
    ];

    protected $hidden = [];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'city_id');
    }
}
