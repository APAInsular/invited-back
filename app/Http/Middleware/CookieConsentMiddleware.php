<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class CookieConsentMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Verifica si el usuario dio consentimiento (puedes usar una cookie o una sesión)
        $hasConsented = $request->cookies->get('cookie_consent') === 'true';

        // Si no ha consentido, eliminar cookies no esenciales (ejemplo)
        if (!$hasConsented) {
            $response->headers->removeCookie('tracking_cookie');
            $response->headers->removeCookie('analytics_cookie');
        }

        if ($request->cookies->get('cookie_consent') === 'true') {
            Cookie::queue('analytics_cookie', 'value', 120);
        }

        return $response;
    }
}
