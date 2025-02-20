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
                'Name' => 'Admin User',
                'First_Surname' => 'Pérez',
                'Second_Surname' => 'López',
                'Phone' => '111111111',
                'Email' => 'admin@example.com',
                'password' => 'password123',
                'role' => 'admin',
                'partner' => [
                    'Name' => 'Ana',
                    'First_Surname' => 'Gómez',
                    'Second_Surname' => 'Díaz',
                ]
            ],
            [
                'Name' => 'Partner User',
                'First_Surname' => 'Rodríguez',
                'Second_Surname' => 'Martínez',
                'Phone' => '222222222',
                'Email' => 'partner@example.com',
                'password' => 'password123',
                'role' => 'partner',
                'partner' => [
                    'Name' => 'Luis',
                    'First_Surname' => 'Fernández',
                    'Second_Surname' => 'García',
                ]
            ],
            [
                'Name' => 'Company User',
                'First_Surname' => 'González',
                'Second_Surname' => 'Sánchez',
                'Phone' => '333333333',
                'Email' => 'company@example.com',
                'password' => 'password123',
                'role' => 'company',
                'partner' => [
                    'Name' => 'Elena',
                    'First_Surname' => 'Ramírez',
                    'Second_Surname' => 'Vega',
                ]
            ],
            [
                'Name' => 'Viewer User',
                'First_Surname' => 'Hernández',
                'Second_Surname' => 'Ortiz',
                'Phone' => '444444444',
                'Email' => 'viewer@example.com',
                'password' => 'password123',
                'role' => 'visor',
                'partner' => [
                    'Name' => 'Mario',
                    'First_Surname' => 'Torres',
                    'Second_Surname' => 'Ruiz',
                ]
            ],
        ];

        // Crear cada usuario y asignar roles
        foreach ($users as $userData) {
            $user = User::create([
                'Name' => $userData['Name'],
                'First_Surname' => $userData['First_Surname'],
                'Second_Surname' => $userData['Second_Surname'],
                'Phone' => $userData['Phone'],
                'Email' => $userData['Email'],
                'password' => Hash::make($userData['password']),
            ]);

            // Asignar rol
            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                $user->assignRole($role);
            }

            // Crear pareja asociada
            Partner::create([
                'Name' => $userData['partner']['Name'],
                'First_Surname' => $userData['partner']['First_Surname'],
                'Second_Surname' => $userData['partner']['Second_Surname'],
                'user_id' => $user->id,
            ]);
        }
    }
}
