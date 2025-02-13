<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wedding;
use App\Models\Event;
use DB;
use Illuminate\Support\Facades\Auth;

class WeddingController extends Controller
{
    public function index()
    {
        // Obtener todas las bodas con sus eventos
        return response()->json(Wedding::with('events')->get(), 200);
    }

    public function store(Request $request)
    {
        // $user = Auth::user(); // Obtener el usuario autenticado
        // if (!$user) {
        //     return response()->json(['error' => 'Usuario no autenticado'], 401);
        // }

        // // Obtener el partner asociado
        // $partner = $user->partner;
        // if (!$partner) {
        //     return response()->json(['error' => 'No se encontró pareja asociada al usuario'], 404);
        // }

        // Validar solo los datos de la boda y eventos (sin user_id ni partner_id)
        $request->validate([
            'Dress_Code' => ['required', 'string', 'max:255'],
            'Wedding_Date' => ['required', 'date'],
            'Music' => ['required', 'string', 'max:255'],
            'foodType' => ['required', 'string', 'max:255'],
            'template' => ['required', 'string', 'max:255'],
            'guestCount' => ['required', 'string', 'max:255'],
            'customMessage' => ['required', 'string', 'max:255'],
            'events' => ['required', 'array'],
            'events.*.name' => ['required', 'string', 'max:255'],
            'events.*.description' => ['nullable', 'string'],
            'events.*.time' => ['required', 'date_format:H:i'],
            'events.*.location' => ['nullable', 'string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            // Crear la boda con los IDs obtenidos automáticamente
            $wedding = Wedding::create([
                // 'user_id' => $user->id,
                // 'partner_id' => $partner->id,
                // 'user_name' => $user->Name,
                // 'partner_name' => $partner->Name,
                'Dress_Code' => $request->Dress_Code,
                'Wedding_Date' => $request->Wedding_Date,
                'Music' => $request->Music,
                'guestCount' => $request->guestCount,
                'template' => $request->template,
                'foodType' => $request->foodType,
                'customMessage'=> $request->customMessage,
            ]);

            // Crear los eventos asociados a la boda
            foreach ($request->events as $eventData) {
                Event::create([
                    'name' => $eventData['name'],
                    'description' => $eventData['description'] ?? null,
                    'time' => $eventData['time'],
                    'location' => $eventData['location'] ?? null,
                    'wedding_id' => $wedding->id,
                ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Boda y eventos creados correctamente',
                'wedding' => $wedding->load('events')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al guardar la boda y eventos',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        $wedding = Wedding::with('events')->find($id);
        if (!$wedding) {
            return response()->json(['error' => 'Boda no encontrada'], 404);
        }
        return response()->json($wedding, 200);
    }

    public function update(Request $request, $id)
    {
        $wedding = Wedding::find($id);
        if (!$wedding) {
            return response()->json(['error' => 'Boda no encontrada'], 404);
        }

        $request->validate([
            'Dress_Code' => ['string', 'max:255'],
            'Wedding_Date' => ['date'],
            'Music' => ['string', 'max:255'],
        ]);

        DB::beginTransaction();
        try {
            $wedding->update($request->only(['Dress_Code', 'Wedding_Date', 'Music']));

            DB::commit();
            return response()->json(['message' => 'Boda actualizada correctamente', 'wedding' => $wedding], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al actualizar la boda', 'details' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $wedding = Wedding::find($id);
        if (!$wedding) {
            return response()->json(['error' => 'Boda no encontrada'], 404);
        }

        DB::beginTransaction();
        try {
            // Eliminar eventos relacionados automáticamente por `onDelete('cascade')`
            $wedding->delete();
            DB::commit();
            return response()->json(['message' => 'Boda y eventos eliminados correctamente'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar la boda', 'details' => $e->getMessage()], 500);
        }
    }
}
