<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a nuestra plataforma</title>
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
            border: 1px solid #ddd; /* Borde tipo card */
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
        .button {
            display: inline-block;
            background-color: #F19292
            ;
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
        a{
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            🌟 ¡Bienvenido, {{ $user->name }}! 🌟
        </div>
        <div class="content">
            <p>Estamos encantados de tenerte en nuestra plataforma. 🎉</p>
            <p>Explora, conecta y disfruta de la mejor experiencia con nosotros.</p>
            <p>Si necesitas ayuda, no dudes en contactarnos.</p>
            <a href="https://invited.es" class="button">Ir a la Plataforma</a>
        </div>
        <div class="footer">
            <p>Saludos,</p>
            <p><strong>El equipo de Invited</strong></p>
        </div>
        <p>Este mensaje ha sido enviado automáticamente por Invited.es, una aplicación desarrollada por APA Insular SL. Si usted no es el destinatario previsto, por favor notifique al remitente y elimine este correo. Para conocer cómo tratamos sus datos personales y ejercer sus derechos, consulte nuestra <a href="https://invited.es/politica-de-privacidad/" target="_blank">Política de Privacidad y Protección de Datos</a>.</p>
    </div>
</body>
</html>
