<?php

use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\Auth\Register;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JokeController;

Route::get('/', function(){
    return view('welcome');
})->name('welcome');

Route::get('/home', [JokeController::class, 'index'])
    ->middleware('auth')
    ->name('home');

//Jokes routes:
Route::middleware('auth')->group(function() {
    Route::get('/jokes/search', [JokeController::class, 'search'])->name('search');
    Route::get('/jokes/create', [JokeController::class, 'create'])->name('jokes.create');
    Route::post('/jokes', [JokeController::class, 'store'])->name('jokes.store');
    Route::post('/jokes/save-from-api', [JokeController::class, 'saveFromApi'])->name('jokes.save-from-api');
    Route::get('/jokes/{joke}/edit', [JokeController::class, 'edit'])->name('jokes.edit');
    Route::put('/jokes/{joke}', [JokeController::class, 'update'])->name('jokes.update');
    Route::delete('/jokes/{joke}', [JokeController::class, 'destroy'])->name('jokes.destory');
});

//Auth routes:
Route::view('/register', 'auth.register')
    ->middleware('guest')
    ->name('register');
Route::post('/register', Register::class)
    ->middleware('guest');
Route::view('/login', 'auth.login')
    ->middleware('guest')
    ->name('login');
Route::post('/logout', Logout::class)
    ->middleware('auth')
    ->name('logout');

