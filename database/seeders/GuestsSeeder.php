<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guest;

class GuestsSeeder extends Seeder
{
    public function run()
    {
        Guest::create([
            'name' => 'Carlos',
            'firstSurname' => 'González',
            'secondSurname' => 'Fernández',
            'extraInformation' => 'Invitado especial',
            'allergy' => 'Ninguna',
            'feeding' => 'Normal',
            'wedding_id' => 1 // Asegurar que la boda existe
        ]);

        Guest::create([
            'name' => 'María',
            'firstSurname' => 'Rodríguez',
            'secondSurname' => 'López',
            'extraInformation' => 'Familiar de los novios',
            'allergy' => 'Gluten',
            'feeding' => 'Vegetariana',
            'wedding_id' => 1
        ]);
    }
}
