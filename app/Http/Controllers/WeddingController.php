<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Location;
use App\Models\User;
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
        return response()->json(Wedding::with('events', 'user.partner', )->get(), 200);
    }

    public function getTotalGuestsAndAttendants($weddingId)
    {
        // Obtenemos la boda y contamos los guests y attendants relacionados
        $total = Wedding::where('id', $weddingId)
            ->withCount('guests') // Cuenta los invitados (guests)
            ->withCount([
                'guests as attendants_count' => function ($query) {
                    $query->withCount('attendants'); // Cuenta los asistentes de cada invitado
                }
            ])
            ->first();

        // Sumamos el número de guests y attendants
        $totalGuestsAndAttendants = $total->guests_count + $total->attendants_count;

        // Devolvemos el total
        return response()->json([
            'total_guests_and_attendants' => $totalGuestsAndAttendants,
        ]);
    }

    public function updateEvent(Request $request, $id)
    {
        try {
            // Encontrar el evento
            $event = Event::findOrFail($id);

            // Validar los datos, permitiendo actualización parcial con "sometimes"
            $validatedData = $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'description' => ['sometimes', 'nullable', 'string'],
                'time' => ['sometimes', 'date_format:H:i'],
                'location.city' => ['sometimes', 'string', 'max:255'],
                'location.country' => ['sometimes', 'string', 'max:255'],
            ]);

            // Actualizar solo los campos enviados
            $event->update($validatedData);

            return response()->json([
                'message' => 'Evento actualizado correctamente',
                'event' => $event
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar el evento',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserWeddings($id)
    {
        try {
            // Buscar al usuario
            $user = User::findOrFail($id);

            // Obtener todas sus bodas con la información relacionada (por ejemplo, ubicación y eventos)
            $weddings = Wedding::where('user_id', $id)->with(['location', 'events'])->get();

            return response()->json([
                'message' => 'Bodas encontradas',
                'weddings' => $weddings
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener las bodas',
                'details' => $e->getMessage()
            ], 500);
        }
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
        // Validación de los datos entrantes
        $validatedData = $request->validate([
            'weddingDate' => ['nullable', 'date'],
            'musicTitle' => ['nullable', 'string', 'max:255'],
            'musicUrl' => ['nullable', 'string', 'max:255'],
            'foodType' => ['nullable', 'string', 'max:255'],
            'template' => ['nullable', 'string', 'max:255'],
            'guestCount' => ['nullable', 'integer'],
            'customMessage' => ['nullable', 'string', 'max:255'],
            'dressCode' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'array'],
            'location.city' => ['nullable', 'string'],
            'location.country' => ['nullable', 'string'],
            'coverImage' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'images.*' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        try {
            // Buscar la boda
            $wedding = Wedding::findOrFail($id);

            // Si se envía una nueva imagen de portada, actualizarla
            if ($request->hasFile('coverImage')) {
                $coverImagePath = $request->file('coverImage')->store('weddings/covers', 'public');
                $wedding->coverImage = $coverImagePath;
            }

            // Si se proporciona una nueva ubicación, actualizarla
            if (!empty($request->location)) {
                $location = Location::firstOrCreate([
                    'city' => $request->location['city'] ?? $wedding->location->city,
                    'country' => $request->location['country'] ?? $wedding->location->country,
                ]);
                $wedding->location_id = $location->id;
            }

            // Si se suben nuevas imágenes de galería, agregarlas
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('weddings/gallery', 'public');
                    Image::create([
                        'wedding_id' => $wedding->id,
                        'image' => $imagePath,
                    ]);
                }
            }

            // Actualizar solo los campos enviados en la solicitud
            $wedding->fill($validatedData);
            $wedding->save();

            return response()->json([
                'message' => 'Boda actualizada correctamente',
                'wedding' => $wedding
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar la boda',
                'details' => $e->getMessage()
            ], 500);
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
