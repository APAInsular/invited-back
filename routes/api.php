<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

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

// Rutas de autenticación
Route::middleware('cors')->post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth:sanctum');

// Rutas protegidas por autenticación
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    // Rutas protegidas por rol
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', function () {
            return response()->json(['message' => 'Bienvenido, Admin']);
        });
    });

    Route::middleware(['role:editor'])->group(function () {
        Route::get('/editor/dashboard', function () {
            return response()->json(['message' => 'Bienvenido, Editor']);
        });
    });
});
