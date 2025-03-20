<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        $data = $request->only('name', 'email', 'message');

        Mail::to('contacto@invited.es')->send(new ContactMessageMail($data));

        return response()->json(['message' => 'Correo enviado correctamente'], 200);
    }
}
