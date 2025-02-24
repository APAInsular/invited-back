<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run()
    {
        DB::table('locations')->insert([
            [
                
                'City' => 'New York',
                'Country' => 'USA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                
                'City' => 'Washington',
                'Country' => 'USA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                
                'City' => 'Los Angeles',
                'Country' => 'USA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
               
                'City' => 'Paris',
                'Country' => 'France',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                
                'City' => 'Tokyo',
                'Country' => 'Japan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
