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
            'dressCode' => 'negro',
            'weddingDate' => '2025-06-15',
            'musicUrl' => 'htpps://musica',
            'musicTitle' => 'la gozadera',
            'customMessage' => 'Bienvenidos a nuestra boda',
            'foodType' => 'Vegetariano',
            'guestCount' => 150,
            'template' => 'classic',
            'location_id'=> 3,
            'groomDescription'=>'majete',
            'brideDescription'=>'guapa'
        ]);
    }
}
