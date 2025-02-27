<?php

namespace App\Http\Controllers;

use App\Models\Attendant;
use Illuminate\Http\Request;
use App\Models\Guest;
use DB;


class GuestController extends Controller
{
    public function index()
    {
        return response()->json(Guest::with('attendants')->get(), 200);
    }

    public function store(Request $request)
    {
        // Validar los datos principales
        $request->validate([
            'name' => ['required', 'string', 'max:400'],
            'firstSurname' => ['required', 'string', 'max:400'],
            'secondSurname' => ['required', 'string', 'max:400'],
            'extraInformation' => ['nullable', 'string'],
            'allergy' => ['nullable', 'string'],
            'feeding' => ['nullable', 'string', 'max:400'],
            'wedding_id' => ['required', 'exists:weddings,id'],
            'attendants' => ['required', 'array'],
            'attendants.*.name' => ['required', 'string', 'max:255'],
            'attendants.*.firstSurname' => ['nullable', 'string'],
            'attendants.*.secondSurname' => ['nullable', 'string'],
            'attendants.*.age' => ['nullable', 'integer'],
        ]);


        DB::beginTransaction(); // Iniciar transacción
        try {
            // Crear el Guest
            $guest = Guest::create([
                'name' => $request->name,
                'firstSurname' => $request->firstSurname,
                'secondSurname' => $request->secondSurname,
                'extraInformation' => $request->extraInformation,
                'allergy' => $request->allergy,
                'feeding' => $request->feeding,
                'wedding_id' => $request->wedding_id
            ]);


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


            DB::commit(); // Guardar en la BD

            return response()->json([
                'message' => 'Invitado y acompañantes creados exitosamente',
                'guest' => $guest->load('attendants'),
                // 'attendants' => $attendantsData
            ], 201);

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

    public function destroy($id)
    {
        $guest = Guest::find($id);

        if (!$guest) {
            return response()->json(['message' => 'Guest not found'], 404);
        }

        $guest->delete();

        return response()->json(['message' => 'Guest deleted'], 200);
    }
}
