<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/rapport-activite/apercu', [App\Http\Controllers\RapportActiviteController::class, 'apercu']);
