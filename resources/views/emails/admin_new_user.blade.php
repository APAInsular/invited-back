<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Usuario Registrado</title>
</head>
<body>
    <h2>Se ha registrado un nuevo usuario</h2>
    <p><strong>Nombre:</strong> {{ $user->name }} {{ $user->firstSurname }} {{ $user->secondSurname }}</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Teléfono:</strong> {{ $user->phone }}</p>
    <p><strong>Pareja:</strong> {{ $user->partner->name }} {{ $user->partner->firstSurname }}</p>
</body>
</html>
