<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\AdminUserNotificationMail;
use App\Models\Partner;
use Auth;
use DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use App\Mail\WelcomeUserMail;



class RegisteredUserController extends Controller
{



    public function index()
    {
        return response()->json(User::with('partner')->get(), 200);
    }
    public function store(Request $request)
{
    $request->validate([
        'data.name' => ['required', 'string', 'max:255'],
        'data.firstSurname' => ['required', 'string', 'max:255'],
        'data.secondSurname' => ['required', 'string', 'max:255'],
        'data.phone' => ['required', 'string', 'max:255'],
        'data.email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'data.password' => ['required', 'confirmed', Rules\Password::defaults()],
        'data.partnerName' => ['required', 'string', 'max:255'],
        'data.partnerFirstSurname' => ['required', 'string', 'max:255'],
        'data.partnerSecondSurname' => ['required', 'string', 'max:255'],
        'token' => ['required', 'string'],
    ]);

    // Verificar reCAPTCHA
    $secretKey = env('RECAPTCHA_SECRET_KEY');
    try {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $request->token,
        ]);

        $result = $response->json();

        if (!$result['success'] || $result['score'] < 0.5) {
            throw ValidationException::withMessages(['token' => ['reCAPTCHA verification failed.']]);
        }
    } catch (\Exception $e) {
        \Log::error('reCAPTCHA verification error: ' . $e->getMessage());
        throw ValidationException::withMessages(['token' => ['reCAPTCHA verification error.']]);
    }

    DB::beginTransaction();
    try {
        // Crear usuario
        $user = User::create([
            'name' => $request->data['name'],
            'firstSurname' => $request->data['firstSurname'],
            'secondSurname' => $request->data['secondSurname'],
            'phone' => $request->data['phone'],
            'email' => $request->data['email'],
            'password' => Hash::make($request->data['password']),
        ]);

        // Asignar rol con Spatie
        $user->assignRole($request->data['role']);

        // Crear pareja asociada
        $partner = Partner::create([
            'name' => $request->data['partnerName'],
            'firstSurname' => $request->data['partnerFirstSurname'],
            'secondSurname' => $request->data['partnerSecondSurname'],
            'user_id' => $user->id,
        ]);

        // Confirmar transacción antes de enviar el correo
        DB::commit();

        // Generar token (si usas Sanctum)
        $token = $user->createToken('AuthToken')->plainTextToken;

        // Intentar enviar el correo SIN afectar la BD
        try {
            Mail::to(users: $user->email)->send(new WelcomeUserMail($user));
            Mail::to('contacto@invited.es')->send(new AdminUserNotificationMail($user));
        } catch (\Exception $e) {
            \Log::error("Error al enviar el correo: " . $e->getMessage());
        }

        return response()->json([
            'message' => 'Usuario y Partner creados correctamente',
            'user' => $user->load('roles'),
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
    public function updateUser(Request $request, $id)
    {
        try {
            // Encontrar el usuario
            $user = User::findOrFail($id);

            // Validar solo los campos que llegan en la petición
            $validatedData = $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'firstSurname' => ['sometimes', 'string', 'max:255'],
                'secondSurname' => ['sometimes', 'string', 'max:255'],
                'phone' => ['sometimes', 'string', 'max:255'],
                'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'password' => ['sometimes', 'confirmed', Rules\Password::defaults()],
            ]);

            // Si se envía una contraseña, la encriptamos antes de actualizar
            if ($request->filled('password')) {
                $validatedData['password'] = Hash::make($request->password);
            }

            // Actualizar solo los campos enviados en la petición
            $user->update($validatedData);

            // Si se envían datos de la pareja, actualizarlos con sometimes
            if ($request->filled('partner')) {
                $partnerData = $request->validate([
                    'partner.name' => ['sometimes', 'string', 'max:255'],
                    'partner.firstSurname' => ['sometimes', 'string', 'max:255'],
                    'partner.secondSurname' => ['sometimes', 'string', 'max:255'],
                ]);

                // Obtener la pareja actual o crear una nueva
                $user->partner()->updateOrCreate([], $partnerData['partner']);
            }

            return response()->json([
                'message' => 'Usuario y pareja actualizados correctamente',
                'user' => $user->load('partner') // Cargar la relación de pareja
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar el usuario',
                'details' => $e->getMessage()
            ], 500);
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
