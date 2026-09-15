<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RapportActiviteFrontImageController;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use App\Http\Controllers\TestMultipagesController;
use App\Http\Controllers\TestMultipagesV2Controller;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/rapport-activite/apercu', [App\Http\Controllers\RapportActiviteController::class, 'apercu']);
Route::get('/rapport-activite/apercu-mpdf', [App\Http\Controllers\RapportActiviteMpdfController::class, 'apercu']);
Route::get('/rapport-activite/overlay', [App\Http\Controllers\RapportOverlayController::class, 'apercu']);
Route::get('/rapport-activite/apercu-spatie', [App\Http\Controllers\RapportActiviteSpatieController::class, 'apercu']);
Route::get('/rapport-activite/apercu-chartjs', [App\Http\Controllers\RapportActiviteChartjsController::class, 'apercu']);
Route::get('/rapport-activite/debug-chartjs', function (App\Services\RapportActiviteService $service) {
    return view('pdf.rapport-activite-chartjs', $service->construireDonnees());
});
// routes/web.php
Route::get('/tableau-de-bord', function (\App\Services\RapportActiviteService $service) {
    return view('tableau-de-bord', $service->construireDonnees());
});
Route::post('/rapport-activite/depuis-front', [RapportActiviteFrontImageController::class, 'generer'])
    ->withoutMiddleware([PreventRequestForgery::class]);

Route::get('/test-multipages/dompdf', [TestMultipagesController::class, 'dompdf']);
Route::get('/test-multipages/mpdf',   [TestMultipagesController::class, 'mpdf']);
Route::get('/test-multipages/spatie', [TestMultipagesController::class, 'spatie']);

Route::get('/test-v2/dompdf', [TestMultipagesV2Controller::class, 'dompdf']);
Route::get('/test-v2/mpdf',   [TestMultipagesV2Controller::class, 'mpdf']);
Route::get('/test-v2/spatie', [TestMultipagesV2Controller::class, 'spatie']);