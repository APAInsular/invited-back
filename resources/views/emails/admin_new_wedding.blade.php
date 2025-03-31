
@php use Illuminate\Support\Str; @endphp


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>🔔 Nueva boda creada</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f8f8f8; padding: 30px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 10px; border: 1px solid #ddd;">
        <h2 style="color: #F19292;">🔔 Nueva boda creada</h2>

        <p><strong>👤 Usuario:</strong> {{ $wedding->user->name }} ({{ $wedding->user->email }})</p>
        <p><strong>📅 Fecha de la boda:</strong> {{ $wedding->weddingDate }}</p>
        <p><strong>📍 Ubicación:</strong> {{ $wedding->location->city ?? 'No especificada' }}, {{ $wedding->location->country ?? '' }}</p>
        <p><strong>👥 Invitados:</strong> {{ $wedding->guestCount ?? 'N/A' }}</p>

        <a href="https://invited.es/invitacion/{{ Str::slug($wedding->user->name) }}-{{ Str::slug($wedding->user->partner->name) }}/{{ $wedding->id }}">Enlace a la invitación de la boda</a>

        {{-- <p style="margin-top: 30px;">Puedes revisar esta boda desde el panel de administrador.</p> --}}
        {{-- <a href="{{ url('/admin/weddings/'.$wedding->id) }}" style="background-color: #F19292; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Ir al Panel</a> --}}

        <p style="margin-top: 40px; font-size: 12px; color: #999;">Este correo ha sido generado automáticamente por el sistema de Invited.</p>
    </div>
</body>
</html>
