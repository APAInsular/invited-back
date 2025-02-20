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
            'Name' => 'Jose',
            'First_Surname' => 'González',
            'Second_Surname' => 'Fernández',
            'age' => 22,
            'guest_id' => 1 // Asegurar que la boda existe
        ]);

        Attendant::create([
            'Name' => 'Sara',
            'First_Surname' => 'Rodríguez',
            'Second_Surname' => 'López',
            'age' => 4,
            'guest_id' => 1
        ]);
    }
}
