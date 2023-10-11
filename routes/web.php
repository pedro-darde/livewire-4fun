<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ScreenController;
use App\Http\Controllers\Users;
use Illuminate\Support\Facades\Route;

Route::middleware(\App\Http\Middleware\RedirectIfAuthenticated::class)->group(function () {
    Route::get('register', [AuthController::class, 'register'])->name('register');
    Route::post('register', [AuthController::class, 'saveRegister'])->name('register');
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'doLogin'])->name('login');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/welcome', function () {
        return Inertia\Inertia::render('Welcome', [
            'user' => [
                'name' => 'Pedro Darde'
            ]
        ]);
    });
    Route::resource("users", Users::class);
    Route::resource("patients", PatientController::class);
    Route::resource("appointments", AppointmentController::class);
    Route::resource("screens", ScreenController::class);
    Route::get('/screens/dynamic/{screen}', [ScreenController::class, 'getDynamic']);
})->name('dashboard');


