<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\WeddingController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/couples', [RegisteredUserController::class, 'storeCouple']);

// Rutas de autenticación
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'login']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum');

Route::get('/users', [RegisteredUserController::class, 'index']); // Obtener todos los usuarios con sus parejas
Route::post('/users', [RegisteredUserController::class, 'store']); // Crear un usuario con su pareja
Route::get('/users/{id}', [RegisteredUserController::class, 'show']); // Obtener un usuario por ID
Route::put('/users/{id}', [RegisteredUserController::class, 'update']); // Actualizar un usuario y su pareja
Route::delete('/users/{id}', [RegisteredUserController::class, 'destroy']); // Eliminar un usuario y su pareja


// CRUD de bodas y eventos
Route::get('/weddings', [WeddingController::class, 'index']); // Listar todas las bodas
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/weddings', [WeddingController::class, 'store']);
});
Route::get('/weddings/{id}', [WeddingController::class, 'show']); // Ver una boda específica
Route::put('/weddings/{id}', [WeddingController::class, 'update']); // Actualizar boda
Route::delete('/weddings/{id}', [WeddingController::class, 'destroy']); // Eliminar boda y eventos


// Rutas protegidas por autenticación
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        $user = Auth::user(); // Obtener el usuario autenticado

        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        return response()->json($user->load('partner'), 200);
    });

    // Rutas protegidas por rol
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return response()->json(['message' => 'Bienvenido, Admin']);
        });
    });

    Route::middleware(['role:company'])->group(function () {
        Route::get('/editor/dashboard', function () {
            return response()->json(['message' => 'Bienvenido, Empresa']);
        });
    });
});

use App\Http\Controllers\GuestController;
use App\Http\Controllers\AttendantController;

Route::middleware('auth:sanctum')->group(function () {
    // Rutas para Guests
    Route::get('/guests', [GuestController::class, 'index']);
    Route::post('/guests', [GuestController::class, 'store']);
    Route::get('/guests/{id}', [GuestController::class, 'show']);
    Route::put('/guests/{id}', [GuestController::class, 'update']);
    Route::delete('/guests/{id}', [GuestController::class, 'destroy']);

    // Rutas para Attendants
    Route::get('/attendants', [AttendantController::class, 'index']);
    Route::post('/attendants', [AttendantController::class, 'store']);
    Route::get('/attendants/{id}', [AttendantController::class, 'show']);
    Route::put('/attendants/{id}', [AttendantController::class, 'update']);
    Route::delete('/attendants/{id}', [AttendantController::class, 'destroy']);
});

