<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completa tu pago - Invited</title>
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
            🎉 ¡Solo falta un paso para activar tu boda en Invited! 🎉
        </div>
        <div class="content">
            <p>Tu invitacion digital ha sido creada con éxito en Invited. Para poder obtener el enlace y enviarlo a tus invitados, sólo necesitas completar el pago.</p>
            <div class="details">
                <p><strong>📅 Fecha de la boda:</strong> {{ $wedding->weddingDate }}</p>
                <p><strong>📍 Ubicación:</strong> {{ $wedding->location['city'] ?? 'No especificada' }}, {{ $wedding->location['country'] ?? 'No especificada' }}</p>
                <p><strong>👥 Número de invitados:</strong> {{ $wedding->guestCount }}</p>
            </div>
            <p>Puedes realizar el pago de tres maneras:</p>
            <p>¡¡Aprovecha este increíble descuento de un 15% hasta el 30 de mayo!!</p>
            <h3>💳 Pago online:</h3>
            <p>Haz clic en el siguiente botón para pagar de forma rápida y segura a través de Revolut:</p>
            <a href="https://checkout.revolut.com/pay/be4caade-33d3-44c9-8b0a-04d207c7c6f3" class="button">Realizar pago online</a>

            {{-- <h3>Pago mediante QR:</h3>
            <img src="{{ Storage::url('qr/qr.png') }}" alt="Código QR"> --}}
            <h3>🏦 Transferencia bancaria:</h3>
            <p>Si prefieres realizar el pago mediante transferencia, usa los siguientes datos:</p>
            <p><strong>🧾 Beneficiario:</strong> APA INSULAR SOCIEDAD LIMITADA</p>
            <p><strong>🏦 IBAN:</strong> ES6915830001139386225614</p>
            <p><strong>✉️ Referencia:</strong> Indica tu email de registro en invited.es</p>

            <p>Una vez realizado el pago, por favor envía el comprobante a <strong>contacto@invited.es</strong> para validar la transacción.</p>
        </div>
        <div class="footer">
            <p>Si tienes alguna duda, no dudes en escribirnos.</p>
            <p><strong>El equipo de Invited</strong></p>
        </div>
    </div>
</body>
</html>
