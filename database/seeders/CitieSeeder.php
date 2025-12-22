<?php

namespace Database\Seeders;

use App\Models\Cities;
use Illuminate\Database\Seeder;

class CitieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cities::create([
            'name' => 'Amsterdam',
            'country' => 'Netherlands',
        ]);
    }
}
