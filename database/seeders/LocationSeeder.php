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
                'Population' => '1,000,000',
                'Postal_Code' => 10001,
                'City' => 'New York',
                'Country' => 'USA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Population' => '500,000',
                'Postal_Code' => 20002,
                'City' => 'Washington',
                'Country' => 'USA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Population' => '2,000,000',
                'Postal_Code' => 30003,
                'City' => 'Los Angeles',
                'Country' => 'USA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Population' => '1,200,000',
                'Postal_Code' => 40004,
                'City' => 'Paris',
                'Country' => 'France',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'Population' => '500,000',
                'Postal_Code' => 50005,
                'City' => 'Tokyo',
                'Country' => 'Japan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
