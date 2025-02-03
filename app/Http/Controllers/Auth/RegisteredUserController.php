<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'Name' => ['required', 'string', 'max:255'],
            'First_Surname' => ['required', 'string', 'max:255'],
            'Second_Surname' => ['required', 'string', 'max:255'],
            'Phone' => ['required', 'string', 'max:255'],
            'Email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'Name' => $request->Name,
            'First_Surname' => $request->First_Surname,
            'Second_Surname' => $request->Second_Surname,
            'Phone' => $request->Phone,
            'Email' => $request->Email,
            'password' => Hash::make($request->password),
        ]);

        // Asignar el rol 'user' por defecto
        $user->assignRole('partner');

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }
}
