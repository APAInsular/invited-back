<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Wedding;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class ImageController extends Controller
{
    public function destroy($id)
    {
        // 1) Buscar la imagen en la BD
        $image = Image::findOrFail($id);

        // OPCIONAL: Verificar permisos (admin o dueño de la boda)
        // if (auth()->user()->cannot('delete', $image)) { 
        //     abort(403, 'No autorizado');
        // }

        // 2) Eliminar el archivo físico del disco (S3 o local)
        // Asumiendo que en "image" guardas la RUTA (p.e. "weddings/gallery/..."), no la URL completa
        Storage::disk('s3')->delete($image->image);

        // 3) Eliminar el registro de la BD
        $image->delete();

        return response()->json([
            'message' => 'Imagen eliminada correctamente'
        ], 200);
    }

    public function addGalleryImages(Request $request, $weddingId)
    {
        // Validar que se envíe un array de imágenes
        $request->validate([
            'images' => ['required', 'array'],
            'images.*' => ['file', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'], // Máximo 10 MB por imagen
        ]);

        // Buscar la boda
        $wedding = Wedding::findOrFail($weddingId);

        // Opcional: define una carpeta usando el ID de la boda o el slug, por ejemplo:
        $folderPath = "weddings/{$wedding->id}/gallery";

        $storedImages = [];

        DB::beginTransaction();

        try {
            foreach ($request->file('images') as $imageFile) {
                if ($imageFile->isValid()) {
                    // Almacenar la imagen en el disco S3 en la carpeta designada
                    $imagePath = $imageFile->store($folderPath, 's3');

                    // Guardar la ruta en la base de datos (se recomienda guardar solo la ruta relativa)
                    $img = Image::create([
                        'wedding_id' => $wedding->id,
                        'image' => $imagePath,
                    ]);

                    $storedImages[] = $img;
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Imágenes añadidas correctamente.',
                'images' => $storedImages,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al añadir las imágenes.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
