<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\CountryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Routes d'authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



// Routes pour les pays
Route::apiResource('countries', CountryController::class);

// Routes pour les drapeaux
Route::post('/countries/{id}/flag', [CountryController::class, 'updateFlag']);
Route::get('/countries/{id}/flag', [CountryController::class, 'getFlag']);


// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});