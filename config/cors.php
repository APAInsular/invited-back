<?php

return [
    'paths' => ['api/*'],  // Permitir solo las rutas que empiezan con /api/
    'allowed_methods' => ['*'],  // Permitir todos los métodos HTTP
    'allowed_origins' => ['http://localhost:3000',' https://invited-front.vercel.app/'],  // Agregar el dominio de tu frontend
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],  // Permitir todos los headers
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,  // Permitir credenciales (tokens de autenticación, cookies)
];
