<?php

namespace Database\Seeders;

use App\Models\Attendant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Attendant::create([
            'name' => 'Jose',
            'firstSurname' => 'González',
            'secondSurname' => 'Fernández',
            'age' => 22,
            'guest_id' => 1 // Asegurar que la boda existe
        ]);

        Attendant::create([
            'name' => 'Sara',
            'firstSurname' => 'Rodríguez',
            'secondSurname' => 'López',
            'age' => 4,
            'guest_id' => 1
        ]);
    }
}
