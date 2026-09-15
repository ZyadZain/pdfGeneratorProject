<?php

namespace App\Http\Controllers;

use App\Services\RapportActiviteService;
use Spatie\LaravelPdf\Facades\Pdf;

class RapportActiviteSpatieController extends Controller
{
    public function apercu(RapportActiviteService $service)
    {
        $donnees = $service->construireDonnees();

        // Même vue Blade que les tests DomPDF et mPDF — comparaison à
        // source identique. Chromium (via Browsershot) l'interprète avec
        // un vrai moteur de rendu web, contrairement aux deux précédents.
        return Pdf::view('pdf.rapport-activite', $donnees)
            ->format('a4')
            ->name('rapport-activite-spatie.pdf');
    }
}

/*
Route à ajouter dans routes/web.php :

use App\Http\Controllers\RapportActiviteSpatieController;

Route::get('/rapport-activite/apercu-spatie', [RapportActiviteSpatieController::class, 'apercu']);
*/