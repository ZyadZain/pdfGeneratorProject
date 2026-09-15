<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf as DomPdf;
use Mpdf\Mpdf;
use Spatie\LaravelPdf\Facades\Pdf as SpatiePdf;

/**
 * Test multi-pages comparatif.
 *
 * Le MÊME Blade (pdf.test-multipages) est rendu par les 3 moteurs.
 * Toute différence observée vient donc du moteur, pas du template.
 *
 * Points testés :
 *   A - en-tête répété sur chaque page (position: fixed)
 *   B - pied de page + pagination automatique (counter(page)/counter(pages))
 *   C - saut de page explicite (page-break-before)
 *   D - bloc insécable (page-break-inside: avoid)
 *   E - <thead> répété quand un tableau se coupe
 */
class TestMultipagesController extends Controller
{
    public function dompdf()
    {
        $pdf = DomPdf::loadView('pdf.test-multipages');

        return $pdf->stream('test-multipages-dompdf.pdf');
    }

    public function mpdf()
    {
        $html = view('pdf.test-multipages')->render();

        // Marges à 0 : c'est la règle @page du CSS qui pilote la mise en
        // page ici, on laisse le moteur l'appliquer sans la contredire.
        $mpdf = new Mpdf([
            'format' => 'A4',
            'tempDir' => storage_path('app/mpdf-tmp'),
        ]);

        $mpdf->WriteHTML($html);

        return response($mpdf->Output('test-multipages-mpdf.pdf', 'S'))
            ->header('Content-Type', 'application/pdf');
    }

    public function spatie()
    {
        return SpatiePdf::view('pdf.test-multipages')
            ->format('a4')
            ->name('test-multipages-spatie.pdf');
    }
}

/*
Routes à ajouter dans routes/web.php :

use App\Http\Controllers\TestMultipagesController;

Route::get('/test-multipages/dompdf', [TestMultipagesController::class, 'dompdf']);
Route::get('/test-multipages/mpdf',   [TestMultipagesController::class, 'mpdf']);
Route::get('/test-multipages/spatie', [TestMultipagesController::class, 'spatie']);
*/