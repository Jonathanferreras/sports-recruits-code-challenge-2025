<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamsController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/player/all', [PlayerController::class, 'index']);
Route::post('/teams/generate', [TeamsController::class, 'generate']);
