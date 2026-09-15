<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

/**
 * Test WeasyPrint.
 *
 * Contrairement à DomPDF et mPDF (bibliothèques PHP appelées en direct),
 * WeasyPrint est un exécutable externe. Laravel produit le HTML, puis
 * appelle le binaire en ligne de commande — ici via Docker.
 *
 * Point important pour l'évaluation : WeasyPrint n'est PAS un service
 * réseau. C'est un processus lancé, qui lit un fichier et en écrit un
 * autre, puis s'arrête. Aucun port ouvert, aucune API exposée —
 * contrairement à Gotenberg.
 *
 * En production (conteneur Linux), on appellerait directement
 * `weasyprint` sans passer par Docker, WeasyPrint étant installé dans
 * l'image applicative.
 */
class TestWeasyprintController extends Controller
{
    public function apercu()
    {
        $dossier = storage_path('app/weasyprint');
        File::ensureDirectoryExists($dossier);

        $fichierHtml = $dossier . DIRECTORY_SEPARATOR . 'source.html';
        $fichierPdf  = $dossier . DIRECTORY_SEPARATOR . 'resultat.pdf';

        File::put($fichierHtml, view('pdf.test-multipages-weasyprint')->render());

        // Le dossier local est monté dans le conteneur sous /data.
        $process = new Process([
            'docker', 'run', '--rm',
            '-v', $dossier . ':/data',
            'weasyprint-local',
            '/data/source.html',
            '/data/resultat.pdf',
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
Route à ajouter dans routes/web.php :

use App\Http\Controllers\TestWeasyprintController;

Route::get('/test-v2/weasyprint', [TestWeasyprintController::class, 'apercu']);
*/