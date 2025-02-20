<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;

class EventsSeeder extends Seeder
{
    public function run()
    {
        Event::create([
            'name' => 'Ceremonia',
            'wedding_id' => 1, // Asegúrate de que la boda exista
            'description' => 'Ceremonia en la iglesia principal',
            'time' => '2025-06-15 16:00:00',
            'location' => 'Iglesia San Pedro'
        ]);

        Event::create([
            'name' => 'Recepción',
            'wedding_id' => 1,
            'description' => 'Cena y baile en el salón principal',
            'time' => '2025-06-15 19:00:00',
            'location' => 'Salón Real'
        ]);
    }
}
