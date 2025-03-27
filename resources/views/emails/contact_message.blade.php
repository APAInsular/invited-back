<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Mensaje de Contacto</title>
</head>
<body>
    <h2>Nuevo mensaje de contacto</h2>
    <p><strong>Nombre:</strong> {{ $data['formData.name'] }}</p>
    <p><strong>Correo:</strong> {{ $data['formData.email'] }}</p>
    <p><strong>Mensaje:</strong></p>
    <p>{{ $data['formData.message'] }}</p>
</body>
</html>
