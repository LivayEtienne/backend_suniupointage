<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\CohorteController;
use App\Http\Controllers\Api\ApprenantController;

use App\Http\Controllers\Api\HistoriqueController;

Route::get('/historiques', [HistoriqueController::class, 'index']); // Afficher tous les historiques
Route::post('/historiques', [HistoriqueController::class, 'store']); // Créer un nouvel historique

Route::resource('apprenants', ApprenantController::class);

Route::apiResource('departments', DepartmentController::class);

Route::put('/cohortes/{id}', [CohorteController::class, 'update']);
Route::delete('/cohortes/{id}', [CohorteController::class, 'destroy']);

Route::get('/cohortes', [CohorteController::class, 'index']);
Route::post('/cohortes', [CohorteController::class, 'store']);


Route::apiResource('users', UserController::class);
Route::apiResource('employees', EmployeeController::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
