<?php

namespace App\Http\Controllers;

use App\Models\Location;
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

    public function getFullWeddingInfo($id)
    {
        $wedding = Wedding::with([
            'user.partner',      // Información del usuario que creó la boda
            'events.location',    // Eventos asociados
            'guests.attendants', // Invitados junto con sus acompañantes
            'location'
        ])->find($id);

        if (!$wedding) {
            return response()->json(['error' => 'Boda no encontrada'], 404);
        }

        return response()->json([
            'message' => 'Detalles completos de la boda',
            'wedding' => $wedding
        ], 200);
    }
    public function getInfoWithoutGuests($id)
    {
        $wedding = Wedding::with([
            'user.partner',      // Información del usuario que creó la boda
            'events.location',    // Eventos asociados
            'location'
        ])->find($id);

        if (!$wedding) {
            return response()->json(['error' => 'Boda no encontrada'], 404);
        }

        return response()->json([
            'message' => 'Detalles completos de la boda',
            'wedding' => $wedding
        ], 200);
    }


    public function store(Request $request)
    {
        // Validar solo los datos de la boda y eventos (sin user_id ni partner_id)
        $request->validate([
            'user_id' => ['required', 'integer'],
            'weddingDate' => ['required', 'date'],
            'musicTitle' => ['required', 'string', 'max:255'],
            'musicUrl' => ['required', 'string', 'max:255'],
            'foodType' => ['required', 'string', 'max:255'],
            'template' => ['required', 'string', 'max:255'],
            'guestCount' => ['required', 'string', 'max:255'],
            'customMessage' => ['required', 'string', 'max:255'],
            'events' => ['required', 'array'],
            'events.*.name' => ['required', 'string', 'max:255'],
            'events.*.description' => ['nullable', 'string'],
            'events.*.time' => ['required', 'date_format:H:i'],
            'events.*.location' => ['required', 'array'],
            'events.*.location.City' => ['required', 'string'],
            'events.*.location.Country' => ['required', 'string'],
            
            'location' => ['required', 'array'],
            'location.City' => ['required', 'string'],
            'location.Country' => ['required', 'string'],
            
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'], // Validación para la imagen
            'dressCode' => ['nullable', 'string', 'max:255'],

        ]);

        DB::beginTransaction();
        try {
            // Guardar la ubicación principal de la boda
            $weddingLocation = Location::firstOrCreate([
                'City' => $request->location['City'],
                'Country' => $request->location['Country']
            ], [
                'Population' => $request->location['Population'] ?? null,
                'Postal_Code' => $request->location['Postal_Code'] ?? null,
            ]);

            // Crear la boda con los datos validados
            $wedding = Wedding::create([
                'user_id' => $request->user_id,
                'Dress_Code' => $request->dressCode,
                'Wedding_Date' => $request->weddingDate,
                'Music_Title' => $request->musicTitle,
                'Music_Url' => $request->musicUrl,
                'Food_Type' => $request->foodType,
                'Template' => $request->template,
                'Guest_Count' => $request->guestCount,
                'Custom_Message' => $request->customMessage,
                'location_id' => $weddingLocation->id,  // Asociar la ubicación de la boda
            ]);

            // Subir la imagen si se proporciona
            if ($request->hasFile('image')) {
                // Obtener la imagen
                $image = $request->file('image');
                // Generar un nombre único para la imagen
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                // Mover la imagen al directorio 'images' en public
                $image->move(public_path('images'), $imageName);
                // Actualizar la boda con la ruta de la imagen
                $wedding->update([
                    'image' => 'images/' . $imageName
                ]);
            }

            // Crear los eventos asociados a la boda
            foreach ($request->events as $eventData) {
                $eventLocation = Location::firstOrCreate([
                    'City' => $eventData['location']['City'],
                    'Country' => $eventData['location']['Country']
                ], [
                    'Population' => $eventData['location']['Population'] ?? null,
                    'Postal_Code' => $eventData['location']['Postal_Code'] ?? null,
                ]);

                Event::create([
                    'wedding_id' => $wedding->id,
                    'location_id' => $eventLocation->id,
                    'name' => $eventData['name'],
                    'description' => $eventData['description'] ?? null,
                    'time' => $eventData['time'],
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
