<?php

namespace App\Http\Controllers;

use App\Mail\GuestAcceptedMail;
use App\Models\Attendant;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Guest;
use DB;
use App\Models\Wedding;
use Illuminate\Support\Facades\Http;
use Mail;


class GuestController extends Controller
{
    public function index()
    {
        return response()->json(Guest::with('attendants')->get(), 200);
    }

    public function updateGuest(Request $request, $wedding_id, $guest_id)
    {
        try {
            // Verificar que la boda existe
            $wedding = Wedding::findOrFail($wedding_id);

            // Verificar que el invitado existe y pertenece a la boda
            $guest = Guest::where('id', $guest_id)->where('wedding_id', $wedding_id)->firstOrFail();

            // Validar datos (solo actualizar lo que se envíe)
            $validatedData = $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'firstSurname' => ['sometimes', 'string', 'max:255'],
                'secondSurname' => ['sometimes', 'string', 'max:255'],
                'extraInformation' => ['sometimes', 'string', 'nullable'],
                'allergy' => ['sometimes', 'string', 'nullable'],
                'feeding' => ['sometimes', 'string', 'nullable'],
            ]);

            // Actualizar solo los datos proporcionados
            $guest->update($validatedData);

            return response()->json([
                'message' => 'Invitado actualizado correctamente',
                'guest' => $guest
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar el invitado',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function updateAttendant(Request $request, $wedding_id, $guest_id, $attendant_id)
    {
        try {
            // Verificar que la boda existe
            $wedding = Wedding::findOrFail($wedding_id);

            // Verificar que el invitado existe y pertenece a la boda
            $guest = Guest::where('id', $guest_id)->where('wedding_id', $wedding_id)->firstOrFail();

            // Verificar que el acompañante existe y pertenece al invitado
            $attendant = Attendant::where('id', $attendant_id)->where('guest_id', $guest_id)->firstOrFail();

            // Validar datos (solo actualizar lo que se envíe)
            $validatedData = $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'firstSurname' => ['sometimes', 'string', 'max:255'],
                'secondSurname' => ['sometimes', 'string', 'max:255'],
                'age' => ['sometimes', 'integer'],
            ]);

            // Actualizar los datos del acompañante
            $attendant->update($validatedData);

            return response()->json([
                'message' => 'Acompañante actualizado correctamente',
                'attendant' => $attendant
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar el acompañante',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function getWeddingGuests($id)
    {
        try {
            // Buscar la boda con sus datos
            $wedding = Wedding::with('location', 'events')->findOrFail($id);

            // Obtener todos los invitados con sus acompañantes
            $guests = Guest::where('wedding_id', $id)
                ->with('attendants') // Relación con acompañantes
                ->get();

            return response()->json([
                'message' => 'Boda e invitados encontrados',
                'wedding' => $wedding,
                'guests' => $guests
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener la boda y sus invitados',
                'details' => $e->getMessage()
            ], 500);
        }
    }



    public function store(Request $request)
    {
        // Validar los datos principales
        $request->validate([
            'name' => ['required', 'string', 'max:400'],
            'firstSurname' => ['required', 'string', 'max:400'],
            'secondSurname' => ['nullable', 'string', 'max:400'],
            'extraInformation' => ['nullable', 'string'],
            'allergy' => ['nullable', 'string'],
            'feeding' => ['nullable', 'string', 'max:400'],
            'wedding_id' => ['required', 'exists:weddings,id'],
            'attendants' => ['nullable', 'array'],
            'attendants.*.name' => ['nullable', 'string', 'max:255'],
            'attendants.*.firstSurname' => ['nullable', 'string'],
            'attendants.*.secondSurname' => ['nullable', 'string'],
            'attendants.*.age' => ['nullable', 'integer'],
        ]);


        DB::beginTransaction(); // Iniciar transacción
        try {
            // Crear el Guest
            $wedding = Wedding::with('user', )->findOrFail($request->wedding_id);

            $guest = Guest::create([
                'name' => $request->name,
                'firstSurname' => $request->firstSurname,
                'secondSurname' => $request->secondSurname,
                'extraInformation' => $request->extraInformation,
                'allergy' => $request->allergy,
                'feeding' => $request->feeding,
                'wedding_id' => $request->wedding_id
            ]);


            if (!empty($request->attendants)) {
                foreach ($request->attendants as $attendant) {
                    Attendant::create([
                        'name' => $attendant['name'],
                        'firstSurname' => $attendant['firstSurname'],
                        'secondSurname' => $attendant['secondSurname'],
                        'age' => $attendant['age'],
                        'guest_id' => $guest->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }


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


            DB::commit(); // Guardar en la BD
            try {
                // Obtener los dueños de la boda (marido y mujer)
                $owners = [$wedding->user->email];

                // Si tienes más dueños (por ejemplo, el cónyuge), agrégalos aquí
                if ($wedding->partner_email ?? false) {
                    $owners[] = $wedding->partner_email;
                }

                // Enviar correo a los dueños de la boda
                Mail::to($owners)->send(new GuestAcceptedMail($guest, $wedding));

                if (count(Mail::failures()) > 0) {
                    throw new \Exception("Falló el envío de correos: " . implode(", ", Mail::failures()));
                }

            } catch (\Exception $e) {
                \Log::error("Error al enviar el correo de confirmación del invitado: " . $e->getMessage());
            }

        } catch (\Exception $e) {
            DB::rollBack(); // Revertir cambios si hay un error

            return response()->json([
                'error' => 'Error al guardar el invitado y sus acompañantes',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        $guest = Guest::with('attendants')->find($id);

        if (!$guest) {
            return response()->json(['message' => 'Guest not found'], 404);
        }

        return response()->json($guest, 200);
    }

    public function update(Request $request, $id)
    {
        $guest = Guest::find($id);

        if (!$guest) {
            return response()->json(['message' => 'Guest not found'], 404);
        }

        $guest->update($request->all());

        return response()->json($guest, 200);
    }

    public function deleteGuest($wedding_id, $guest_id)
    {
        try {
            $wedding = Wedding::findOrFail($wedding_id);
            $guest = Guest::where('id', $guest_id)->where('wedding_id', $wedding_id)->firstOrFail();

            // Eliminar todos los acompañantes del invitado antes de eliminarlo
            $guest->attendants()->delete();

            // Eliminar el invitado
            $guest->delete();

            return response()->json([
                'message' => 'Invitado y sus acompañantes eliminados correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar el invitado',
                'details' => $e->getMessage()
            ], 500);
        }
    }

}
