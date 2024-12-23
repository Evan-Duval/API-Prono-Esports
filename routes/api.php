<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\TeamGameController;
use App\Http\Controllers\AuthController;

Route::prefix('auth')->group( function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('reset-password', [AuthController::class, 'resetpassword']);
    Route::post('update-password', [AuthController::class,'changepassword']);

    Route::group(['middleware' => 'auth:sanctum'], function() {
      Route::get('logout', [AuthController::class, 'logout']);
      Route::get('user', [AuthController::class, 'user']);
    });
});


Route::prefix('players')->group(function() {
    // Route:: méthode Http, méthode pour associer notre controlleur à une méthode (index pour la ligne 13, méthode store pour l14)
    Route::get('all', PlayerController::class . '@index');
    Route::get('search', PlayerController::class .'@search');

    Route::post('create', PlayerController::class . '@store');

    Route::delete('delete/{id}', PlayerController::class .'@destroy');
});


Route::prefix('teams')->group(function() {
    Route::get('all', TeamController::class . '@index');

    Route::post('create', TeamController::class . '@store');

    Route::delete('delete/{id}', TeamController::class .'@destroy');
});


Route::prefix('games')->group(function() {
    Route::get('all', GameController::class .'@index');

    Route::post('create', GameController::class .'@store');

    Route::delete('delete/{id}', GameController::class .'@destroy');
});

Route::prefix('jeux-equipes')->group(function() {
    Route::get('all', TeamGameController::class .'@index');

    Route::post('create', TeamGameController::class . '@store');
});
