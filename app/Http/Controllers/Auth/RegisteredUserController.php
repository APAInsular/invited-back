<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Auth;
use DB;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;



class RegisteredUserController extends Controller
{

 

    public function index()
    {
        return response()->json(User::with('partner')->get(), 200);
    }
    public function store(Request $request)
    {
        $request->validate([
            'Name' => ['required', 'string', 'max:255'],
            'First_Surname' => ['required', 'string', 'max:255'],
            'Second_Surname' => ['required', 'string', 'max:255'],
            'Phone' => ['required', 'string', 'max:255'],
            'Email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'Partner_Name' => ['required', 'string', 'max:255'],
            'Partner_First_Surname' => ['required', 'string', 'max:255'],
            'Partner_Second_Surname' => ['required', 'string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            // Crear usuario
            $user = User::create([
                'Name' => $request->Name,
                'First_Surname' => $request->First_Surname,
                'Second_Surname' => $request->Second_Surname,
                'Phone' => $request->Phone,
                'Email' => $request->Email,
                'password' => Hash::make($request->password),
            ]);

            // Crear partner con un nombre y apellidos diferentes
            $partner = Partner::create([
                'Name' => $request->Partner_Name,
                'First_Surname' => $request->Partner_First_Surname,
                'Second_Surname' => $request->Partner_Second_Surname,
                'user_id' => $user->id, // Llave foránea conectando con la tabla users
            ]);

            // Auth::login($user);
            Auth::attempt(['Email' => $request->Email, 'password' => $request->password]);
            //Si entra en el 200
            // if (Auth::check()) {
            //     return response()->json(['message' => 'Usuario autenticado correctamente'], 200); 
            // } else {
            //     return response()->json(['error' => 'Autenticación fallida'], 401);
            // }
            


            // Si usas Laravel Sanctum o Passport, generamos un token de acceso
            $token = $user->createToken('TokenName')->plainTextToken; // Si usas Sanctum
            // $token = $user->createToken('TokenName', ['role:admin'])->plainTextToken; // Si necesitas roles o permisos

            // Devolver la respuesta con el usuario, el mensaje y el token
            DB::commit();
            return response()->json([
                'message' => 'Usuario y Partner creados correctamente',
                'user' => $user,
                'token' => $token,  // Solo si estás usando tokens (Sanctum o Passport)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear los registros', 'details' => $e->getMessage()], 500);
        }
    }
    public function show($id)
    {
        $user = User::with('partner')->find($id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        return response()->json($user, 200);
    }
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // Validamos los campos que pueden ser enviados
        $request->validate([
            'Name' => ['string', 'max:255'],
            'First_Surname' => ['string', 'max:255'],
            'Second_Surname' => ['string', 'max:255'],
            'Phone' => ['string', 'max:255'],
            'Email' => ['email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['confirmed', Rules\Password::defaults()],
            'Partner_Name' => ['string', 'max:255'],
            'Partner_First_Surname' => ['string', 'max:255'],
            'Partner_Second_Surname' => ['string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            // Actualizamos solo los campos del usuario que fueron enviados en la solicitud
            $userFields = $request->only(['Name', 'First_Surname', 'Second_Surname', 'Phone', 'Email', 'password']);
            // Si el campo de la contraseña está presente, lo actualizamos
            if ($request->filled('password')) {
                $userFields['password'] = Hash::make($request->password);
            }

            // Actualizamos solo los campos del usuario si están presentes
            if (count($userFields) > 0) {
                $user->update($userFields);
                $user->save();
            }

            // Actualizamos los campos del partner solo si están presentes
            $partner = Partner::where('user_id', $id)->first();
            if ($partner) {
                // Verificamos cada campo del partner individualmente
                if ($request->filled('Partner_Name')) {
                    $partner->Name = $request->Partner_Name;
                }
                if ($request->filled('Partner_First_Surname')) {
                    $partner->First_Surname = $request->Partner_First_Surname;
                }
                if ($request->filled('Partner_Second_Surname')) {
                    $partner->Second_Surname = $request->Partner_Second_Surname;
                }

                // Si hemos hecho cambios, guardamos la actualización del partner
                if ($partner->isDirty()) {
                    $partner->save();
                }
            }

            DB::commit();
            return response()->json(['message' => 'Usuario y/o Partner actualizados correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar los registros', 'details' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        DB::beginTransaction();
        try {
            Partner::where('user_id', $id)->delete();
            $user->delete();
            DB::commit();
            return response()->json(['message' => 'Usuario y Partner eliminados correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar los registros', 'details' => $e->getMessage()], 500);
        }
    }
}
