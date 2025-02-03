<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Limpiar cache de roles y permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            'create invitations',
            'delete invitations',
            'create infopage of invitations',
            'see invitations',
            'see guest',
            'see companies',
            'contact companies',
            'eliminate guest'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles y asignar permisos
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $editorRole = Role::firstOrCreate(['name' => 'partner']);
        $editorRole->givePermissionTo(['create invitations', 'delete invitations', 'see invitations', 'see guest', 'see companies', 'contact companies', 'eliminate guest']);

        $userRole = Role::firstOrCreate(['name' => 'company']);
        $userRole->givePermissionTo(['create infopage of invitations', 'see invitations', 'see guest', 'see companies']);

        // // Asignar rol a un usuario (opcional)
        // $user = \App\Models\User::first(); // Ajusta según tu modelo de usuario
        // if ($user) {
        //     $user->assignRole('admin');
        // }
    }
}
