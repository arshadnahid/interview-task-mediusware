<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use  App\Http\Controllers\TransactionController;
use  App\Http\Controllers\HomeController;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');


    Route::get('/deposit', [TransactionController::class, 'deposit_list'])->name('deposit_list');
    Route::post('/deposit', [TransactionController::class, 'deposit_store'])->name('deposit_store');

    Route::get('/withdrawal', [TransactionController::class, 'withdrawal_list'])->name('withdrawal_list');
    Route::post('/withdrawal', [TransactionController::class, 'withdrawal_store'])->name('withdrawal_store');
});

