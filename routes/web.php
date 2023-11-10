<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ScreenController;
use App\Http\Controllers\Users;
use App\Http\Middleware\UserLogged;
use Illuminate\Support\Facades\Route;

Route::middleware(\App\Http\Middleware\RedirectIfAuthenticated::class)->group(function () {
    Route::get('register', [AuthController::class, 'register'])->name('register');
    Route::post('register', [AuthController::class, 'saveRegister'])->name('register');
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::get('/', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'doLogin'])->name('login');
});

Route::middleware(UserLogged::class)->group(function () {
    Route::get("/logout", [AuthController::class, 'logout'])->name('logout');
    Route::get('/welcome', [DashboardController::class, 'index'])->name('welcome');
    Route::resource("users", Users::class);
    Route::resource("patients", PatientController::class);
    Route::resource("appointments", AppointmentController::class);
    Route::resource("screens", ScreenController::class);
    Route::get('/screens/dynamic/{screen}', [ScreenController::class, 'getDynamic']);
    Route::post('/appointment/{appointment}/changeStatus', [AppointmentController::class, 'changeStatus']);
    Route::post('/note/{appointment}', [NoteController::class, 'store'])->name('saveNote');
    Route::post('/note/{appointment}/{note}', [NoteController::class, 'update'])->name('updateNote');
    Route::post("/downloadFile", [FileController::class, 'download'])->name("downloadFile");
});

Route::get("/aaaaa", [AppointmentController::class, 'test']);
