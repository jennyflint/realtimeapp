<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

});

Route::middleware('auth')->group(function () {
    Route::get('/user/list', [UserController::class, 'index'])
        ->name('user.lists');

    Route::get('/message/{recipientId}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/send/message', [MessageController::class, 'store'])->name('messages.store');

    Route::get('/logout', [LogoutController::class, 'destroy'])->name('logout');

    Route::get('/', function () {
        return view('welcome');
    });
});
