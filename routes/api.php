<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\UsersController;
use App\Http\Middleware\CheckRole;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get("/test", function () {
    return "El backend funciona correctamente";
});

Route::prefix('users')->middleware("jwt.auth")->group(function () {
    Route::get("/who", [AuthController::class, "who"]);
    Route::post("/logout", [AuthController::class, "logout"]);
    Route::post("/refresh", [AuthController::class, "refresh"]);
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

// Poner middleware básico de JWT

Route::get("/movies/plans", [MoviesController::class, "getAllPlans"]);
Route::post('/movies', [MoviesController::class, 'store'])
    ->middleware(CheckRole::class);

Route::apiResource('/movies', MoviesController::class)
    ->except(['store']);


/*
Método HTTP	    Ruta	            Acción del controlador	   Propósito
GET	            /movies	            index	                   Listar todos los recursos
POST	        /movies	            store	                   Crear un recurso
GET	            /movies/{movie}	    show	                   Mostrar un recurso
PUT/PATCH	    /movies/{movie}	    update	                   Actualizar un recurso
DELETE	        /movies/{movie}	    destroy	                   Eliminar un recurso
*/