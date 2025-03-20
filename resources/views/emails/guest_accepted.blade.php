<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Un invitado ha confirmado su asistencia!</title>
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
            background-color: #F19292;
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
            ✅ ¡Nuevo invitado confirmado!
        </div>
        <div class="content">
            <p>¡Buenas noticias! Uno de tus invitados ha confirmado su asistencia a tu boda. 🎉</p>
            <div class="details">
                <p><strong>👤 Invitado:</strong> {{ $guest->name }} {{ $guest->firstSurname }}</p>
                <p><strong>📅 Fecha de la boda:</strong> {{ $wedding->weddingDate }}</p>
                @if(!empty($wedding->location))
                    <p><strong>📍 Ubicación:</strong> {{ $wedding->location['city'] ?? 'No especificada' }}, {{ $wedding->location['country'] ?? 'No especificada' }}</p>
                @endif
                @if(!empty($wedding->dressCode))
                    <p><strong>👗 Código de vestimenta:</strong> {{ $wedding->dressCode }}</p>
                @endif
            </div>
            <a href="{{ url('/wedding/'.$wedding->id.'/guests') }}" class="button">Ver lista de invitados</a>
        </div>
        <div class="footer">
            <p>¡Sigue disfrutando de la planificación de tu gran día! 💍✨</p>
            <p><strong>El equipo de Invited</strong></p>
        </div>
        <p>Este mensaje ha sido enviado automáticamente por Invited.es, una aplicación desarrollada por APA Insular SL. Si usted no es el destinatario previsto, por favor notifique al remitente y elimine este correo. Para conocer cómo tratamos sus datos personales y ejercer sus derechos, consulte nuestra <a href="https://invited.es/politica-de-privacidad/" target="_blank">Política de Privacidad y Protección de Datos</a>.</p>
    </div>
</body>
</html>
