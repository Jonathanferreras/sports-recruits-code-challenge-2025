<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\TeamsController;
use App\Http\Controllers\ResultsController;

Route::get('/', fn () => Inertia::render('App'))->name('app');

Route::get('/results', [ResultsController::class, 'show'])
    ->name('results.show');

require __DIR__.'/auth.php';
