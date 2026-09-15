<?php

namespace App\Http\Controllers;

use App\Services\RapportActiviteService;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;

class RapportActiviteChartjsController extends Controller
{
    public function apercu(RapportActiviteService $service)
    {
        $donnees = $service->construireDonnees();

        return Pdf::view('pdf.rapport-activite-chartjs', $donnees)
            ->format('a4')
            ->withBrowsershot(function (Browsershot $browsershot) {
                $browsershot->waitUntilNetworkIdle();
                $browsershot->setDelay(300);
                // Densité de rendu 2x : Chart.js dessine son canvas en
                // fonction de window.devicePixelRatio, qui suit ce réglage.
                // Sans ça, le canvas est net à l'écran normal mais flou une
                // fois intégré au PDF (rendu en 1x par défaut).
                $browsershot->deviceScaleFactor(2);
            })
            ->name('rapport-activite-chartjs.pdf');
    }
}

/*
Route à ajouter dans routes/web.php :

use App\Http\Controllers\RapportActiviteChartjsController;

Route::get('/rapport-activite/apercu-chartjs', [RapportActiviteChartjsController::class, 'apercu']);
*/