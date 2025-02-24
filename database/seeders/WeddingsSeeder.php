<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wedding;

class WeddingsSeeder extends Seeder
{
    public function run()
    {
        Wedding::create([
            'user_id' => 1, // Ajusta según usuarios existentes
            'Dress_Code' => 'Formal',
            'Wedding_Date' => '2025-06-15',
            'Music' => 'Jazz',
            'customMessage' => 'Bienvenidos a nuestra boda',
            'foodType' => 'Vegetariano',
            'guestCount' => 150,
            'template' => 'classic',
            'location_id'=> 3
        ]);
    }
}
