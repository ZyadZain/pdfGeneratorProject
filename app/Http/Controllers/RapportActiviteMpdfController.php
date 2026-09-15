<?php

namespace App\Http\Controllers;

use App\Services\RapportActiviteService;
use Mpdf\Mpdf;

class RapportActiviteMpdfController extends Controller
{
    public function apercu(RapportActiviteService $service)
    {
        $donnees = $service->construireDonnees();

        // On récupère le HTML compilé de la MÊME vue Blade que pour DomPDF —
        // c'est la seule façon de vraiment comparer le rendu à source identique.
        $html = view('pdf.rapport-activite', $donnees)->render();

        $mpdf = new Mpdf([
            'format' => 'A3',
            'margin_left' => 25,
            'margin_right' => 25,
            'margin_top' => 25,
            'margin_bottom' => 25,
            // mPDF ne réutilise pas le temp dir de Laravel : il faut lui en
            // donner un explicitement, et qu'il existe déjà (voir commande
            // mkdir dans les instructions de mise en place).
            'tempDir' => storage_path('app/mpdf-tmp'),
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('rapport-activite-mpdf.pdf', 'S'))
            ->header('Content-Type', 'application/pdf');
    }
}

/*
Route à ajouter dans routes/web.php :

use App\Http\Controllers\RapportActiviteMpdfController;

Route::get('/rapport-activite/apercu-mpdf', [RapportActiviteMpdfController::class, 'apercu']);
*/