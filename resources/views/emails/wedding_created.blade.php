<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Tu boda ha sido creada!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: 1px solid #ddd;
        }
        .header {
            background-color: #F19292
            ;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            color: white;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            color: #333;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
        }
        .details {
            text-align: left;
            margin-top: 20px;
        }
        .details p {
            font-size: 16px;
            margin: 5px 0;
        }
        .button {
            display: inline-block;
            background-color: #F19292;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
            border: none;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            🎉 ¡Felicidades, tu boda ha sido creada! 🎉
        </div>
        <div class="content">
            <p>Tu boda ha sido registrada exitosamente. Estamos emocionados de acompañarte en este viaje tan especial. 💍✨</p>
            <div class="details">
                <p><strong>📅 Fecha:</strong> {{ $wedding->weddingDate }}</p>
                <p><strong>📍 Ubicación:</strong> {{ $wedding->location['city'] ?? 'No especificada' }}, {{ $wedding->location['country'] ?? 'No especificada' }}</p>
                <p><strong>👥 Invitados:</strong> {{ $wedding->guestCount ?? 'No especificado' }}</p>
                @if(!empty($wedding->dressCode))
                    <p><strong>👗 Código de vestimenta:</strong> {{ $wedding->dressCode }}</p>
                @endif
                @if(!empty($wedding->musicTitle) && !empty($wedding->musicUrl))
                    <p><strong>🎶 Canción especial:</strong> <a href="{{ $wedding->musicUrl }}" target="_blank">{{ $wedding->musicTitle }}</a></p>
                @endif
                @if(!empty($wedding->groomDescription))
                    <p><strong>🤵 Sobre el novio:</strong> {{ $wedding->groomDescription }}</p>
                @endif
                @if(!empty($wedding->brideDescription))
                    <p><strong>👰 Sobre la novia:</strong> {{ $wedding->brideDescription }}</p>
                @endif
                @if(!empty($wedding->customMessage))
                    <p><strong>💌 Mensaje especial:</strong> {{ $wedding->customMessage }}</p>
                @endif
                @if(!empty($wedding->foodType))
                    <p><strong>🍽️ Tipo de comida:</strong> {{ $wedding->foodType }}</p>
                @endif
            </div>
            <a href="{{ url('/dashboard') }}" class="button">Ver mi Boda</a>
        </div>
        <div class="footer">
            <p>Saludos,</p>
            <p><strong>El equipo de Invited</strong></p>
        </div>
    </div>
</body>
</html>
