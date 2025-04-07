<?php

namespace App\Http\Controllers;

use App\Models\Image;
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
}
