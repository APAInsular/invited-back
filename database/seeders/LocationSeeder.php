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
                
                'city' => 'New York',
                'country' => 'USA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                
                'city' => 'Washington',
                'country' => 'USA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                
                'city' => 'Los Angeles',
                'country' => 'USA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
               
                'city' => 'Paris',
                'country' => 'France',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                
                'city' => 'Tokyo',
                'country' => 'Japan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
