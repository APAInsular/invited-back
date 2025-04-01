<?php

namespace App\Http\Controllers;

use App\Mail\AdminUserNotificationMail;
use App\Mail\AdminWeddingNotificationMail;
use App\Mail\WeddingCreatedMail;
use App\Mail\WelcomeUserMail;
use App\Models\Image;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Wedding;
use App\Models\Event;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mail;





class WeddingController extends Controller
{
    public function index()
    {
        // Obtener todas las bodas con sus eventos
        return response()->json(Wedding::with('events', 'user.partner', )->get(), 200);
    }


    public function getTotalGuestsAndAttendantsCount($weddingId)
    {
        // Obtenemos todos los guests relacionados con la boda
        $wedding = Wedding::with('guests.attendants')->find($weddingId);

        // Verificamos si la boda existe
        if (!$wedding) {
            return response()->json([
                'message' => 'Wedding not found'
            ], 404);
        }

        // Contamos el número total de guests
        $totalGuests = $wedding->guests->count();

        // Inicializamos el contador de attendants
        $totalAttendants = 0;

        // Recorremos cada guest y sumamos los attendants
        foreach ($wedding->guests as $guest) {
            $totalAttendants += $guest->attendants->count(); // Sumar el número de attendants de cada guest
        }

        // Devolvemos el total de guests y attendants
        return response()->json([
            'total_guests' => $totalGuests,
            'total_attendants' => $totalAttendants,
            'total_guests_and_attendants' => $totalGuests + $totalAttendants
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
            'location',
            'images'             // Imágenes relacionadas con la boda
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
            'dressCode' => ['required', 'string', 'max:255'],
            'events' => ['required', 'array'],
            'events.*.name' => ['required', 'string', 'max:255'],
            'events.*.description' => ['nullable', 'string'],
            'events.*.time' => ['required', 'date_format:H:i'],
            'events.*.location' => ['nullable', 'array'],
            'events.*.location.city' => ['nullable', 'string'],
            'events.*.location.country' => ['nullable', 'string'],
            'location' => ['required', 'array'],
            'location.city' => ['required', 'string'],
            'location.country' => ['required', 'string'],
            'coverImage' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'],
            'images.*' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'],
        ]);

        DB::beginTransaction();
        try {
            // Guardar la ubicación principal de la boda
            $weddingLocation = Location::firstOrCreate([
                'city' => $request->location['city'],
                'country' => $request->location['country']
            ]);

            $user = User::with('partner')->findOrFail($request->user_id);

            // 2) Generar nombres de carpeta a partir de user->name y user->partner->name
            // (Asegúrate de que la relación "partner" devuelva un objeto con "name")
            $folderName = Str::slug($user->name . '-' . $user->partner->name) . '_' . time();
            $weddingPath = "weddings/{$folderName}";

            // Guardar imagen de portada
            $coverImagePath = null;
            if ($request->hasFile('coverImage') && $request->file('coverImage')->isValid()) {
                // Guardará en la carpeta "weddings/<slug>/cover" con el disco "public"
                $coverImagePath = $request->file('coverImage')->store("{$weddingPath}/cover", 'public');
            }



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
                'coverImage' => $request->coverImage ? $request->coverImage->store('weddings/covers', 'public') : null,
            ]);


            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if ($image->isValid()) {
                        $imagePath = $image->store("{$weddingPath}/gallery", 'public');

                        Image::create([
                            'wedding_id' => $wedding->id,
                            'image' => $imagePath,
                        ]);
                    }
                }
            }
            // Subir la imagen de portada (coverImage)
            // if ($request->hasFile('coverImage')) {
            //     if ($request->file('coverImage')->isValid()) {
            //         $coverImagePath = $request->file('coverImage')->store('weddings/covers', 'public');
            //         // Guardar la ruta de la imagen de portada en la base de datos
            //         Image::create([
            //             'wedding_id' => $wedding->id,
            //             'image' => $coverImagePath,
            //         ]);
            //     } else {
            //         throw new \Exception('La imagen de portada no es válida');
            //     }
            // }

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
            // if ($request->hasFile('images')) {
            //     foreach ($request->file('images') as $image) {
            //         if ($image->isValid()) {
            //             $imagePath = $image->store('weddings/gallery', 'public');

            //             // Guardar la ruta de la imagen en la base de datos
            //             Image::create([
            //                 'wedding_id' => $wedding->id,
            //                 'image' => $imagePath,
            //             ]);
            //         } else {
            //             throw new \Exception('Una o más imágenes no son válidas');
            //         }
            //     }
            // }

            DB::commit();
            try {
                // Obtener el usuario de la boda
                $user = User::findOrFail($request->user_id);

                // Enviar correo al usuario creador de la boda
                Mail::to($user->email)->send(new WeddingCreatedMail($wedding));

                // Enviar correo de notificación al administrador
                $wedding->load('user', 'location');
                Mail::to('contacto@invited.es')->send(new AdminWeddingNotificationMail($wedding));

                if (count(Mail::failures()) > 0) {
                    throw new \Exception("Falló el envío de correos: " . implode(", ", Mail::failures()));
                }

            } catch (\Exception $e) {
                \Log::error("Error al enviar el correo: " . $e->getMessage());
            }

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

    public function updateWedding(Request $request, $id)
    {
        try {
            // Buscar la boda
            $wedding = Wedding::findOrFail($id);

            // Validar solo los campos enviados
            $validatedData = $request->validate([
                'user_id' => ['sometimes', 'integer'],
                'weddingDate' => ['sometimes', 'date'],
                'musicTitle' => ['sometimes', 'string', 'max:255'],
                'musicUrl' => ['sometimes', 'string', 'max:255'],
                'foodType' => ['sometimes', 'string', 'max:255'],
                'template' => ['sometimes', 'string', 'max:255'],
                'guestCount' => ['sometimes', 'integer'],
                'customMessage' => ['sometimes', 'string', 'max:255'],
                'dressCode' => ['sometimes', 'string', 'max:255'],
                'location' => ['sometimes', 'array'],
                'location.city' => ['sometimes', 'string', 'max:255'],
                'location.country' => ['sometimes', 'string', 'max:255'],
                'coverImage' => ['sometimes', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'images.*' => ['sometimes', 'file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'events' => ['sometimes', 'array'],
                'events.*.id' => ['sometimes', 'integer', 'exists:events,id'],
                'events.*.name' => ['sometimes', 'string', 'max:255'],
                'events.*.description' => ['sometimes', 'nullable', 'string'],
                'events.*.time' => ['sometimes', 'date_format:H:i'],
                'events.*.location.city' => ['sometimes', 'string', 'max:255'],
                'events.*.location.country' => ['sometimes', 'string', 'max:255'],
            ]);

            // Actualizar los datos de la boda
            $wedding->update($validatedData);

            // Actualizar eventos si se envían
            if ($request->has('events')) {
                foreach ($request->events as $eventData) {
                    if (isset($eventData['id'])) {
                        $event = Event::findOrFail($eventData['id']);
                        $event->update([
                            'name' => $eventData['name'] ?? $event->name,
                            'description' => $eventData['description'] ?? $event->description,
                            'time' => $eventData['time'] ?? $event->time,
                            'location' => [
                                'city' => $eventData['location']['city'] ?? $event->location['city'],
                                'country' => $eventData['location']['country'] ?? $event->location['country'],
                            ],
                        ]);
                    }
                }
            }

            // Actualizar imágenes si se envían
            if ($request->hasFile('coverImage')) {
                $coverPath = $request->file('coverImage')->store('weddings/covers', 'public');
                $wedding->update(['coverImage' => $coverPath]);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $image->store('weddings/images', 'public');
                    $wedding->images()->create(['path' => $imagePath]);
                }
            }

            return response()->json([
                'message' => 'Boda actualizada correctamente',
                'wedding' => $wedding->load('events', 'images')
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
