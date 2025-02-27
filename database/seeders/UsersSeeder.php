<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Partner;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Definir usuarios con sus roles
        $users = [
            [
                'name' => 'Admin User',
                'firstSurname' => 'Pérez',
                'secondSurname' => 'López',
                'phone' => '111111111',
                'email' => 'admin@example.com',
                'password' => 'password123',
                'role' => 'admin',
                'partner' => [
                    'name' => 'Ana',
                    'firstSurname' => 'Gómez',
                    'secondSurname' => 'Díaz',
                ]
            ],
            [
                'name' => 'Partner User',
                'firstSurname' => 'Rodríguez',
                'secondSurname' => 'Martínez',
                'phone' => '222222222',
                'email' => 'partner@example.com',
                'password' => 'password123',
                'role' => 'partner',
                'partner' => [
                    'name' => 'Luis',
                    'firstSurname' => 'Fernández',
                    'secondSurname' => 'García',
                ]
            ],
            [
                'name' => 'Company User',
                'firstSurname' => 'González',
                'secondSurname' => 'Sánchez',
                'phone' => '333333333',
                'email' => 'company@example.com',
                'password' => 'password123',
                'role' => 'company',
                'partner' => [
                    'name' => 'Elena',
                    'firstSurname' => 'Ramírez',
                    'secondSurname' => 'Vega',
                ]
            ],
            [
                'name' => 'Viewer User',
                'firstSurname' => 'Hernández',
                'secondSurname' => 'Ortiz',
                'phone' => '444444444',
                'email' => 'viewer@example.com',
                'password' => 'password123',
                'role' => 'visor',
                'partner' => [
                    'name' => 'Mario',
                    'firstSurname' => 'Torres',
                    'secondSurname' => 'Ruiz',
                ]
            ],
        ];

        // Crear cada usuario y asignar roles
        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'firstSurname' => $userData['firstSurname'],
                'secondSurname' => $userData['secondSurname'],
                'phone' => $userData['phone'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
            ]);

            // Asignar rol
            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                $user->assignRole($role);
            }

            // Crear pareja asociada
            Partner::create([
                'name' => $userData['partner']['name'],
                'firstSurname' => $userData['partner']['firstSurname'],
                'secondSurname' => $userData['partner']['secondSurname'],
                'user_id' => $user->id,
            ]);
        }
    }
}
