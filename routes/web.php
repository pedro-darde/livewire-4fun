<?php

use App\Http\Controllers\Users;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Inertia\Inertia::render('Welcome', [
       'user' => [
           'name' => 'Pedro Darde'
       ]
    ]);
});

Route::resource("users", Users::class);
Route::resource("screens", \App\Http\Controllers\ScreenController::class);
