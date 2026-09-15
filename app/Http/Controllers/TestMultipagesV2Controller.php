<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf as DomPdf;
use Mpdf\Mpdf;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf as SpatiePdf;

/**
 * Test multi-pages comparatif — VERSION 2.
 *
 * Différence majeure avec la v1 : chaque moteur utilise ici SON PROPRE
 * mécanisme natif pour les en-têtes, pieds de page et la pagination,
 * au lieu d'une technique CSS unique qui n'en avantageait qu'un seul.
 *
 * C'est aussi ce qui montre pourquoi ce code DOIT vivre dans une classe
 * centrale de l'environnement : le mécanisme change complètement d'un
 * moteur à l'autre, les développeurs de rapports ne doivent jamais avoir
 * à s'en préoccuper.
 */
class TestMultipagesV2Controller extends Controller
{
    /**
     * DomPDF : position:fixed pour l'en-tête/pied (dans le Blade) +
     * API PHP embarquée pour la pagination.
     * ⚠️ Nécessite 'enable_php' => true dans config/dompdf.php
     */
    public function dompdf()
    {
        $pdf = DomPdf::loadView('pdf.test-multipages-dompdf');

        return $pdf->stream('test-v2-dompdf.pdf');
    }

    /**
     * mPDF : SetHTMLHeader() / SetHTMLFooter(), avec les variables
     * natives {PAGENO} (page courante) et {nbpg} (total).
     */
    public function mpdf()
    {
        $mpdf = new Mpdf([
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 35,    // place réservée à l'en-tête
            'margin_bottom' => 20, // place réservée au pied
            'margin_header' => 10,
            'margin_footer' => 10,
            'tempDir' => storage_path('app/mpdf-tmp'),
        ]);

        $mpdf->SetHTMLHeader('
            <div style="border-bottom: 2px solid #4F46E5; padding-bottom: 4px; font-size: 9pt;">
                <table width="100%"><tr>
                    <td><strong>Boutique Fictive TechStore</strong><br>12 Rue de la Simulation, 75000 Paris</td>
                    <td align="right">Responsable : J. Dupont<br>Période : Septembre 2026</td>
                </tr></table>
            </div>
        ');

        $mpdf->SetHTMLFooter('
            <div style="border-top: 1px solid #999; padding-top: 4px; font-size: 8pt; color: #555;">
                <table width="100%"><tr>
                    <td>Document généré le ' . now()->format('d/m/Y à H:i') . '</td>
                    <td align="right">Page {PAGENO} / {nbpg}</td>
                </tr></table>
            </div>
        ');

        $mpdf->WriteHTML(view('pdf.test-multipages-mpdf')->render());

        return response($mpdf->Output('test-v2-mpdf.pdf', 'S'))
            ->header('Content-Type', 'application/pdf');
    }

    /**
     * Browsershot : fonction d'impression native de Chrome via
     * headerHtml() / footerHtml(). Les classes .pageNumber et
     * .totalPages sont remplies automatiquement par Chrome.
     * Note : le CSS de la page principale ne s'applique PAS à ces
     * blocs — il faut styler en inline, et forcer une taille de police
     * (Chrome les rend en 0 par défaut).
     */
    public function spatie()
    {
        $entete = '
            <div style="width: 100%; font-size: 9px; padding: 0 40px; font-family: Helvetica, Arial, sans-serif;
                        border-bottom: 2px solid #4F46E5; -webkit-print-color-adjust: exact;">
                <table width="100%"><tr>
                    <td><strong>Boutique Fictive TechStore</strong><br>12 Rue de la Simulation, 75000 Paris</td>
                    <td align="right">Responsable : J. Dupont<br>Période : Septembre 2026</td>
                </tr></table>
            </div>';

        $pied = '
            <div style="width: 100%; font-size: 8px; padding: 0 40px; color: #555;
                        font-family: Helvetica, Arial, sans-serif; border-top: 1px solid #999;">
                <table width="100%"><tr>
                    <td>Document généré le ' . now()->format('d/m/Y à H:i') . '</td>
                    <td align="right">Page <span class="pageNumber"></span> / <span class="totalPages"></span></td>
                </tr></table>
            </div>';

        return SpatiePdf::view('pdf.test-multipages-browsershot')
            ->format('a4')
            ->withBrowsershot(function (Browsershot $browsershot) use ($entete, $pied) {
                $browsershot
                    ->showBackground()
                    ->headerHtml($entete)
                    ->footerHtml($pied)
                    // Marges suffisantes pour que l'en-tête et le pied ne
                    // chevauchent pas le contenu (le défaut de la v1).
                    ->margins(30, 15, 20, 15);
            })
            ->name('test-v2-spatie.pdf');
    }
}

/*
Routes à ajouter dans routes/web.php :

use App\Http\Controllers\TestMultipagesV2Controller;

Route::get('/test-v2/dompdf', [TestMultipagesV2Controller::class, 'dompdf']);
Route::get('/test-v2/mpdf',   [TestMultipagesV2Controller::class, 'mpdf']);
Route::get('/test-v2/spatie', [TestMultipagesV2Controller::class, 'spatie']);
*/