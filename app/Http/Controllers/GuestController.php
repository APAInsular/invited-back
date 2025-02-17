<?php

namespace App\Http\Controllers;

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
        // Validar los datos de entrada
        $request->validate([
            'Name' => ['required','string','max:400'],
            'First_Surname' => ['required','string','max:400'],
            'Second_Surname' => ['required','string','max:400'],
            'Extra_Information' => ['nullable','string'],
            'Allergy' => ['nullable','string'],
            'Feeding' => ['nullable','string','max:400'],
            'wedding_id' => ['required','exists:weddings,id']
        ]);
    
        try {
            // Crear el invitado en la BD
            $guest = Guest::create([
                'Name' => $request->Name,
                'First_Surname' => $request->First_Surname,
                'Second_Surname' => $request->Second_Surname,
                'Extra_Information' => $request->Extra_Information,
                'Allergy' => $request->Allergy,
                'Feeding' => $request->Feeding,
                'wedding_id' => $request->wedding_id
            ]);
    
            // Retornar la respuesta JSON con código 201 (creado)
            DB::commit();
            return response()->json([
                'message' => 'Invitado creado exitosamente',
                'guest' => $guest
            ], 201);
            
        } catch (\Exception $e) {
            // Capturar cualquier error y devolverlo en JSON
            return response()->json([
                'error' => 'Error al crear el invitado',
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
