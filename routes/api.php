<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MoviesController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get("/test", function () {
    return "El backend funciona correctamente";
});

Route::prefix('users')->group(function () {
    Route::get("/", [UsersController::class, "index"]);
    Route::post("/login", [AuthController::class, "logIn"]);
    Route::post("/register", [UsersController::class, "register"]);
    Route::put("/update", [UsersController::class, "update"]);
    Route::delete("/{user}", [UsersController::class, "delete"]);
    Route::get("/plan/{id}", [UsersController::class, "getPlan"]);
});

// Poner middleware básico de JWT

Route::get("/movies/plans", [MoviesController::class, "getAllPlans"]);
Route::apiResource("/movies", MoviesController::class);


/*
Método HTTP	    Ruta	            Acción del controlador	   Propósito
GET	            /movies	            index	                   Listar todos los recursos
POST	        /movies	            store	                   Crear un recurso
GET	            /movies/{movie}	    show	                   Mostrar un recurso
PUT/PATCH	    /movies/{movie}	    update	                   Actualizar un recurso
DELETE	        /movies/{movie}	    destroy	                   Eliminar un recurso
*/