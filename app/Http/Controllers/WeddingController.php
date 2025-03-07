<?php

namespace App\Http\Controllers;

use App\Models\Image;
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
        return response()->json(Wedding::with('events','user.partner',)->get(), 200);
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
        // Validar los datos
        $request->validate([
            'user_id' => ['required', 'integer'],
            'weddingDate' => ['required', 'date'],
            'musicTitle' => ['required', 'string', 'max:255'],
            'musicUrl' => ['required', 'string', 'max:255'],
            'foodType' => ['required', 'string', 'max:255'],
            'template' => ['required', 'string', 'max:255'],
            'guestCount' => ['required', 'integer'],
            'customMessage' => ['required', 'string', 'max:255'],
            'events' => ['required', 'array'],
            'events.*.name' => ['required', 'string', 'max:255'],
            'events.*.description' => ['nullable', 'string'],
            'events.*.time' => ['required', 'date_format:H:i'],
            'events.*.location' => ['nullable', 'array'],
            'events.*.location.city' => ['nullable', 'string'],
            'events.*.location.country' => ['nullable', 'string'],
            'location' => ['nullable', 'array'],
            'location.city' => ['nullable', 'string'],
            'location.country' => ['nullable', 'string'],
            'coverImage' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'dressCode' => ['nullable', 'string', 'max:255'],
            'images.*' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        DB::beginTransaction();
        try {
            // Guardar la ubicación principal de la boda
            $weddingLocation = Location::firstOrCreate([
                'city' => $request->location['city'],
                'country' => $request->location['country']
            ]);

            // Crear la boda con los datos validados
            $wedding = Wedding::create([
                'user_id' => $request->user_id,
                'dressCode' => $request->dressCode ?? 'Ninguno',
                'weddingDate' => $request->weddingDate,
                'musicTitle' => $request->musicTitle,
                'musicUrl' => $request->musicUrl,
                'foodType' => $request->foodType,
                'template' => $request->template,
                'guestCount' => $request->guestCount,
                'customMessage' => $request->customMessage,
                'location_id' => $weddingLocation->id,
            ]);

            // Subir la imagen de portada (coverImage)
            if ($request->hasFile('coverImage')) {
                $coverImagePath = $request->file('coverImage')->store('weddings/covers', 'public');
            }

            // Crear los eventos asociados a la boda
            foreach ($request->events as $eventData) {
                $eventLocation = Location::firstOrCreate([
                    'city' => $eventData['location']['city'],
                    'country' => $eventData['location']['country']
                ]);

                Event::create([
                    'wedding_id' => $wedding->id,
                    'location_id' => $eventLocation->id,
                    'name' => $eventData['name'],
                    'description' => $eventData['description'] ?? null,
                    'time' => $eventData['time'],
                ]);
            }

            // Guardar imágenes en la galería
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('weddings/gallery', 'public');
            
                    Image::create([
                        'wedding_id' => $wedding->id,
                        'image' => $imagePath,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Boda y eventos creados correctamente',
                'wedding' => $wedding->load('events.location', 'location', 'images')
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

        DB::beginTransaction();
        try {
            $wedding->update($request->only(['dressCode', 'weddingDate', 'musicUrl', 'musicTitle']));

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
