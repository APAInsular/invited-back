<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔔 Nueva boda creada en Invited</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 40px auto; background: #ffffff; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); text-align: center; border: 1px solid #ddd;">
        <div style="background-color: #F19292; padding: 20px; border-radius: 10px 10px 0 0; color: white; font-size: 24px;">
            🔔 Nueva boda creada
        </div>
        <div style="padding: 20px; color: #333;">
            <p><strong>Un usuario ha creado una nueva boda en Invited:</strong></p>
            <p><strong>👤 Usuario:</strong> {{ $wedding->user->name }} ({{ $wedding->user->email }})</p>
            <p><strong>📅 Fecha:</strong> {{ $wedding->weddingDate }}</p>
            <p><strong>📍 Ubicación:</strong> {{ $wedding->location['city'] ?? 'No especificada' }}, {{ $wedding->location['country'] ?? 'No especificada' }}</p>
            <p><strong>👥 Número de invitados:</strong> {{ $wedding->guestCount }}</p>
            <a href="{{ url('/admin/weddings/'.$wedding->id) }}" style="display: inline-block; background-color: #F19292; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-size: 16px; margin-top: 20px;">Ver detalles</a>
        </div>
        <div style="margin-top: 30px; font-size: 14px; color: #777;">
            <p>Equipo de Invited</p>
        </div>
    </div>
</body>
</html>
