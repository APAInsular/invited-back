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
            'name' => ['required', 'string', 'max:255'],
            'firstSurname' => ['required', 'string', 'max:255'],
            'secondSurname' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'partnerName' => ['required', 'string', 'max:255'],
            'partnerFirstSurname' => ['required', 'string', 'max:255'],
            'partnerSecondSurname' => ['required', 'string', 'max:255'],
            // 'role' => ['required', 'string', 'exists:roles,name'], // Validamos contra la BD
        ]);

        DB::beginTransaction();
        try {
            // Crear usuario
            $user = User::create([
                'name' => $request->name,
                'firstSurname' => $request->firstSurname,
                'secondSurname' => $request->secondSurname,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Asignar rol con Spatie
            $user->assignRole($request->role);

            // Crear pareja asociada
            $partner = Partner::create([
                'name' => $request->partnerName,
                'firstSurname' => $request->partnerFirstSurname,
                'secondSurname' => $request->partnerSecondSurname,
                'user_id' => $user->id,
            ]);

            // Generar token (si usas Sanctum)
            $token = $user->createToken('AuthToken')->plainTextToken;

            DB::commit();

            return response()->json([
                'message' => 'Usuario y Partner creados correctamente',
                'user' => $user->load('roles'), // Cargar roles en la respuesta
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al crear los registros',
                'details' => $e->getMessage(),
            ], 500);
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
            'name' => ['string', 'max:255'],
            'firstSurname' => ['string', 'max:255'],
            'secondSurname' => ['string', 'max:255'],
            'phone' => ['string', 'max:255'],
            'email' => ['email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['confirmed', Rules\Password::defaults()],
            'partnerName' => ['string', 'max:255'],
            'partnerFirstSurname' => ['string', 'max:255'],
            'partnerSecondSurname' => ['string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            // Actualizamos solo los campos del usuario que fueron enviados en la solicitud
            $userFields = $request->only(['name', 'firstSurname', 'secondSurname', 'phone', 'email', 'password']);
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
                if ($request->filled('partnerName')) {
                    $partner->name = $request->partnerName;
                }
                if ($request->filled('partnerFirstSurname')) {
                    $partner->firstSurname = $request->partnerFirstSurname;
                }
                if ($request->filled('partnerSecondSurname')) {
                    $partner->secondSurname = $request->partnerSecondSurname;
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
