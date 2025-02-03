<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Si la solicitud es OPTIONS, devolver una respuesta vacía con código 200
        if ($request->getMethod() == "OPTIONS") {
            return response()->json([], 200);
        }

        return $next($request)
            ->header('Access-Control-Allow-Origin', '*') // Permitir todas las solicitudes de origen
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS') // Métodos permitidos
            ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, X-Auth-Token, Authorization, Accept'); // Encabezados permitidos
    }
}
