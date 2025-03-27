<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'formData.name' => 'required|string',
            'formData.email' => 'required|email',
            'formData.message' => 'required|string',
        ]);

        // Verificar reCAPTCHA
        $secretKey = env('RECAPTCHA_SECRET_KEY');
        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $request->token,
            ]);

            $result = $response->json();

            if (!$result['success'] || $result['score'] < 0.5) {
                throw ValidationException::withMessages(['token' => ['reCAPTCHA verification failed.']]);
            }
        } catch (\Exception $e) {
            \Log::error('reCAPTCHA verification error: ' . $e->getMessage());
            throw ValidationException::withMessages(['token' => ['reCAPTCHA verification error.']]);
        }


        $data = $request->only('formData.name', 'formData.email', 'formData.message');

        Mail::to('contacto@invited.es')->send(new ContactMessageMail($data));

        return response()->json(['message' => 'Correo enviado correctamente'], 200);
    }
}
