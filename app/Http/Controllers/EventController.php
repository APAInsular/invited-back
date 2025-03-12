<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // 📌 Obtener un evento por ID
    public function getEvent($id)
    {
        try {
            $event = Event::findOrFail($id);
            return response()->json([
                'message' => 'Evento encontrado',
                'event' => $event
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener el evento',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // 📌 Crear un nuevo evento
    public function createEvent(Request $request)
    {
        try {
            // Validación de datos
            $validatedData = $request->validate([
                'wedding_id' => ['required', 'integer', 'exists:weddings,id'],
                'name' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
                'time' => ['required', 'date_format:H:i'],
                'location.city' => ['nullable', 'string', 'max:255'],
                'location.country' => ['nullable', 'string', 'max:255'],
            ]);

            // Crear evento
            $event = Event::create($validatedData);

            return response()->json([
                'message' => 'Evento creado correctamente',
                'event' => $event
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al crear el evento',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    // 📌 Actualizar un evento
    public function updateEvent(Request $request, $id)
    {
        try {
            // Encontrar el evento
            $event = Event::findOrFail($id);

            // Validar solo los campos enviados
            $validatedData = $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'description' => ['sometimes', 'nullable', 'string'],
                'time' => ['sometimes', 'date_format:H:i'],
                'location.city' => ['sometimes', 'string', 'max:255'],
                'location.country' => ['sometimes', 'string', 'max:255'],
            ]);

            // Actualizar solo los datos enviados
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

    // 📌 Eliminar un evento
    public function deleteEvent($id)
    {
        try {
            $event = Event::findOrFail($id);
            $event->delete();

            return response()->json([
                'message' => 'Evento eliminado correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar el evento',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
