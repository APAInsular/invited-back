<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UsersSeeder::class, // Seeder para crear usuarios con roles asignados
            WeddingsSeeder::class,
            EventsSeeder::class,
            GuestsSeeder::class,
            AttendantsSeeder::class,
        ]);
    }
}
