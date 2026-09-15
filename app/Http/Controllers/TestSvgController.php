<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf as DomPdf;
use Illuminate\Support\Facades\File;
use Mpdf\Mpdf;
use Symfony\Component\Process\Process;

/**
 * Test SVG comparatif.
 *
 * Enjeu : le front (IGEIA-REFLEX) utilise Recharts, qui produit du SVG.
 * Si le moteur PDF gère correctement le SVG, on peut intégrer les
 * graphiques en vectoriel — net à tout niveau de zoom, souvent plus
 * léger qu'un PNG, et sans le flou constaté avec les canvas.
 *
 * Le même document est rendu par les trois moteurs, avec le SVG inséré
 * de deux façons (inline et data URI).
 */
class TestSvgController extends Controller
{
    public function dompdf()
    {
        return DomPdf::loadView('pdf.test-svg')->stream('test-svg-dompdf.pdf');
    }

    public function mpdf()
    {
        $mpdf = new Mpdf([
            'format' => 'A4',
            'tempDir' => storage_path('app/mpdf-tmp'),
        ]);

        $mpdf->WriteHTML(view('pdf.test-svg')->render());

        return response($mpdf->Output('test-svg-mpdf.pdf', 'S'))
            ->header('Content-Type', 'application/pdf');
    }

    public function weasyprint()
    {
        $dossier = storage_path('app/weasyprint');
        File::ensureDirectoryExists($dossier);

        $fichierHtml = $dossier . DIRECTORY_SEPARATOR . 'svg-source.html';
        $fichierPdf  = $dossier . DIRECTORY_SEPARATOR . 'svg-resultat.pdf';

        File::put($fichierHtml, view('pdf.test-svg')->render());

        $process = new Process([
            'docker', 'run', '--rm',
            '-v', $dossier . ':/data',
            'weasyprint-local',
            '/data/svg-source.html',
            '/data/svg-resultat.pdf',
        ]);

        $process->setTimeout(60);
        $process->run();

        if (!$process->isSuccessful()) {
            return response(
                '<pre>Échec de WeasyPrint :' . PHP_EOL
                . e($process->getErrorOutput() ?: $process->getOutput()) . '</pre>',
                500
            );
        }

        return response(File::get($fichierPdf))
            ->header('Content-Type', 'application/pdf');
    }
}

/*
Routes à ajouter dans routes/web.php :

use App\Http\Controllers\TestSvgController;

Route::get('/test-svg/dompdf',     [TestSvgController::class, 'dompdf']);
Route::get('/test-svg/mpdf',       [TestSvgController::class, 'mpdf']);
Route::get('/test-svg/weasyprint', [TestSvgController::class, 'weasyprint']);
*/