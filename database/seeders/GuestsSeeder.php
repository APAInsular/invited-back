<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guest;

class GuestsSeeder extends Seeder
{
    public function run()
    {
        Guest::create([
            'Name' => 'Carlos',
            'First_Surname' => 'González',
            'Second_Surname' => 'Fernández',
            'Extra_Information' => 'Invitado especial',
            'Allergy' => 'Ninguna',
            'Feeding' => 'Normal',
            'wedding_id' => 1 // Asegurar que la boda existe
        ]);

        Guest::create([
            'Name' => 'María',
            'First_Surname' => 'Rodríguez',
            'Second_Surname' => 'López',
            'Extra_Information' => 'Familiar de los novios',
            'Allergy' => 'Gluten',
            'Feeding' => 'Vegetariana',
            'wedding_id' => 1
        ]);
    }
}
