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
            'Name' => ['required', 'string', 'max:400'],
            'First_Surname' => ['required', 'string', 'max:400'],
            'Second_Surname' => ['required', 'string', 'max:400'],
            'Extra_Information' => ['nullable', 'string'],
            'Allergy' => ['nullable', 'string'],
            'Feeding' => ['nullable', 'string', 'max:400'],
            'wedding_id' => ['required', 'exists:weddings,id'],
            'attendants' => ['required', 'array'],
            'attendants.*.Name' => ['required', 'string', 'max:255'],
            'attendants.*.First_Surname' => ['nullable', 'string'],
            'attendants.*.Second_Surname' => ['nullable', 'string'],
            'attendants.*.age' => ['nullable', 'integer'],
        ]);


        DB::beginTransaction(); // Iniciar transacción
        try {
            // Crear el Guest
            $guest = Guest::create([
                'Name' => $request->Name,
                'First_Surname' => $request->First_Surname,
                'Second_Surname' => $request->Second_Surname,
                'Extra_Information' => $request->Extra_Information,
                'Allergy' => $request->Allergy,
                'Feeding' => $request->Feeding,
                'wedding_id' => $request->wedding_id
            ]);


            foreach ($request->attendants as $attendant) {
                Attendant::create([
                    'Name' => $attendant['Name'],
                    'First_Surname' => $attendant['First_Surname'],
                    'Second_Surname' => $attendant['Second_Surname'],
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
