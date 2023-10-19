<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DynamicScreenController;
use App\Http\Controllers\IconController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ScreenController;
use App\Http\Controllers\SelectController;
use App\Http\Controllers\ServiceSuppliedController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/icons', [IconController::class, 'index']);
    Route::get('/screens', [ScreenController::class, 'all']);


    Route::prefix('dynamic')->group(function () {
        Route::post('/getOptions/{table}', [SelectController::class, 'dynamicItems']);
        Route::post('/save', [DynamicScreenController::class, 'save']);
    });

    Route::get("/patients/loadMore", [PatientController::class, 'loadMore']);
    Route::get("/appointments/loadMore", [AppointmentController::class, 'loadMore']);
    Route::get("/services-supplied", [ServiceSuppliedController::class, 'getAll']);
});
