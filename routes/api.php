<?php

use App\Http\Controllers\EventController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\WeddingController;
use App\Http\Controllers\AttendantController;
use App\Http\Controllers\GuestController;

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
Route::put('/users/{id}', [RegisteredUserController::class, 'updateUser']); // Actualizar un usuario y su pareja
Route::delete('/users/{id}', [RegisteredUserController::class, 'destroy']); // Eliminar un usuario y su pareja
Route::get('user/{id}/weddings', [WeddingController::class, 'getUserWeddings']);


// CRUD de bodas y eventos
Route::get('/weddings', [WeddingController::class, 'index']); // Listar todas las bodas
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/weddings', [WeddingController::class, 'store']);
});
Route::get('/weddings/{id}', [WeddingController::class, 'show']); // Ver una boda específica
Route::put('/weddings/{id}', [WeddingController::class, 'updateWedding']); // Actualizar boda
Route::delete('/weddings/{id}', [WeddingController::class, 'destroy']); // Eliminar boda y eventos

Route::get('/weddings/{id}/full-info', [WeddingController::class, 'getFullWeddingInfo']);
Route::get('/weddings/{id}/info-without-guests', [WeddingController::class, 'getInfoWithoutGuests']);
Route::get('wedding/{id}/invitados', [GuestController::class, 'getWeddingGuests']);


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


// Rutas para Guests
Route::get('/guests', [GuestController::class, 'index']);
Route::post('/guests', [GuestController::class, 'store']);
Route::get('/guests/{id}', [GuestController::class, 'show']);
Route::put('/guests/{id}', [GuestController::class, 'update']);
Route::delete('/wedding/{wedding_id}/guest/{guest_id}', [GuestController::class, 'deleteGuest']);

// Rutas para Attendants
Route::get('/attendants', [AttendantController::class, 'index']);
Route::post('/attendants', [AttendantController::class, 'store']);
Route::get('/attendants/{id}', [AttendantController::class, 'show']);
Route::put('/attendants/{id}', [AttendantController::class, 'update']);
Route::delete('/attendants/{id}', [AttendantController::class, 'destroy']);

Route::put('/wedding/{wedding_id}/guest/{guest_id}/attendant/{attendant_id}', [GuestController::class, 'updateAttendant']);
Route::put('/wedding/{wedding_id}/guest/{guest_id}', [GuestController::class, 'updateGuest']);

Route::get('/wedding/{wedding_id}/numeroInvitados', action: [WeddingController::class, 'getTotalGuestsAndAttendantsCount']);

Route::get('/events/{id}', [EventController::class, 'getEvent']);
Route::post('/events', [EventController::class, 'createEvent']);
Route::put('/events/{id}', [EventController::class, 'updateEvent']);
Route::delete('/events/{id}', [EventController::class, 'deleteEvent']);