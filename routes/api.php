<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\CohorteController;
use App\Http\Controllers\Api\ApprenantController;

use App\Http\Controllers\Api\HistoriqueController;
use App\Http\Controllers\Api\AuthController;


Route::post('/import', [ApprenantController::class, 'importApprenants']); // Importer des apprenants
// routes/api.php
Route::post('user/{id}/upload-photo', [UserController::class, 'uploadPhoto']);

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'getAuthenticatedUser']); // Route pour obtenir l'utilisateur authentifié

// Route pour l'authentification avec cardId
Route::post('/login/cardid', [AuthController::class, 'loginWithCardId']);

//Route::get('/historiques', [HistoriqueController::class, 'index']); // Afficher tous les historiques
//Route::post('/historiques', [HistoriqueController::class, 'store']); // Créer un nouvel historique

Route::resource('apprenants', ApprenantController::class);

Route::post('/apprenants/import', [ApprenantController::class, 'importApprenants']);

Route::apiResource('departments', DepartmentController::class);

Route::delete('/users', [UserController::class, 'deleteMultipleUsers']);

Route::put('/cohortes/{id}', [CohorteController::class, 'update']);
Route::delete('/cohortes/{id}', [CohorteController::class, 'destroy']);
Route::post('/users/import-csv', [UserController::class, 'importFromCSV']);

Route::put('users/{id}/update-uid', [UserController::class, 'updateUid']);


Route::get('/cohortes', [CohorteController::class, 'index']);
Route::post('/cohortes', [CohorteController::class, 'store']);


Route::apiResource('users', UserController::class);
Route::apiResource('employees', EmployeeController::class);


Route::get('/historiques', [HistoriqueController::class, 'index']);
Route::post('/historiques', [HistoriqueController::class, 'store']);
Route::put('/historiques/{id}/activite', [HistoriqueController::class, 'updateActivity']);
Route::get('/historiques/{id}', [HistoriqueController::class, 'show']);


// Route pour obtenir les statistiques des utilisateurs
Route::get('/user-stats', [UserController::class, 'getUserStats']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
