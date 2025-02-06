<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\RegisteredUserController;
class AuthenticatedSessionController extends Controller
{
    public function login(Request $request)
    {
        // Validamos los datos de entrada (correo y contraseña)
        $request->validate([
            'Email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        // Intentamos autenticar al usuario con las credenciales proporcionadas
        if (Auth::attempt(['email' => $request->Email, 'password' => $request->password])) {
            // Si la autenticación fue exitosa, obtenemos el usuario
            $user = Auth::user();

            // Llamamos al método show del controlador RegisteredUserController
            // Aquí pasamos el ID del usuario autenticado para obtener sus detalles adicionales
            $registeredUserController = new RegisteredUserController();
            $userDetails = $registeredUserController->show($user->id);

            // Si estás usando Laravel Sanctum o Passport, puedes generar un token aquí
            $token = $user->createToken('TokenName')->plainTextToken; // Si usas Sanctum

            // Retornar respuesta con el usuario y el token (si es necesario)
            return response()->json([
                'message' => 'Inicio de sesión exitoso',
                // 'user' => $user,
                'user_details' => $userDetails, // Datos adicionales del usuario

                'token' => $token,  // Solo si usas Sanctum o Passport
            ], 200);
        } else {
            // Si las credenciales no son correctas, devolver un error
            return response()->json([
                'error' => 'Credenciales incorrectas',
            ], 401);
        }
    }

    public function destroy(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Cierre de sesión exitoso']);
    }
}
