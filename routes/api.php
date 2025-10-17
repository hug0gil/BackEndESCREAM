<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\SubGenresController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Route;

Route::get("/test", function () {
    return "El backend funciona correctamente";
});

Route::prefix('users')->middleware("jwt.auth")->group(function () {
    Route::get("/who", [AuthController::class, "who"]);
    Route::post("/logout", [AuthController::class, "logout"]);
    Route::post("/refresh", [AuthController::class, "refresh"]);
    Route::put("/changeSubscription", [AuthController::class, "changeSubscription"]);
});


Route::prefix('users')->group(function () {
    Route::get("/", [UsersController::class, "index"]);
    Route::get("/{user}", [UsersController::class, "getUser"]);
    Route::post("/login", [AuthController::class, "logIn"]);
    Route::post("/register", [AuthController::class, "register"]);
    Route::put("/update/{user}", [UsersController::class, "update"]);
    Route::delete("/{user}", [UsersController::class, "delete"]);
    Route::get("/plan/{id}", [UsersController::class, "getPlan"]);
});

Route::prefix('admin')->middleware(CheckRole::class)->group(function () {
    Route::post('/register', [AdminAuthController::class, 'register']);
});


Route::prefix('/movies/genres')->group(function (): void {
    Route::get("/", [SubGenresController::class, "index"]);
});
// Poner middleware básico de JWT

Route::get("/movies/plans", [MoviesController::class, "getAllPlans"]);
Route::get('/movies', [MoviesController::class, 'index']);
Route::get('/movies/{movie}', [MoviesController::class, 'show']);
Route::get('/movies/getImage/{movie}', [MoviesController::class, 'getImage']);
Route::apiResource('/movies', MoviesController::class)->except(['index', 'show'])->middleware(CheckRole::class);






/*
Método HTTP	    Ruta	            Acción del controlador	   Propósito
GET	            /movies	            index	                   Listar todos los recursos
POST	        /movies	            store	                   Crear un recurso
GET	            /movies/{movie}	    show	                   Mostrar un recurso
PUT/PATCH	    /movies/{movie}	    update	                   Actualizar un recurso
DELETE	        /movies/{movie}	    destroy	                   Eliminar un recurso
*/

// Introducir endpoints para administradores, creando AuthAdminController