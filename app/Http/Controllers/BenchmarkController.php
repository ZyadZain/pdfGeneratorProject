<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf as DomPdf;
use Illuminate\Support\Facades\File;
use Mpdf\Mpdf;
use Symfony\Component\Process\Process;

/**
 * Benchmark comparatif : 4 pages, 8 graphiques SVG.
 *
 * Mesure pour chaque moteur : temps de génération, pic mémoire PHP,
 * taille du PDF produit. Trois itérations, on garde la moyenne.
 *
 * Cas particulier de WeasyPrint : on mesure séparément le coût de
 * démarrage d'un conteneur Docker (artefact du poste de développement
 * Windows, qui n'existera pas en production où WeasyPrint sera
 * installé dans l'image applicative). Le temps "corrigé" estime donc
 * la performance réelle attendue en production.
 */
class BenchmarkController extends Controller
{
    private const ITERATIONS = 3;

    public function run()
    {
        $donnees = $this->donneesTest();
        $resultats = [];

        $resultats[] = $this->mesurer('DomPDF', fn() => $this->genererDomPdf($donnees));
        $resultats[] = $this->mesurer('mPDF', fn() => $this->genererMpdf($donnees));
        $resultats[] = $this->mesurer('WeasyPrint (Docker)', fn() => $this->genererWeasyprint($donnees));

        $coutDemarrage = $this->mesurerCoutDemarrageDocker();

        return view('benchmark-resultats', [
            'resultats' => $resultats,
            'coutDemarrage' => $coutDemarrage,
            'iterations' => self::ITERATIONS,
        ]);
    }

    /**
     * Exécute le générateur plusieurs fois et renvoie les moyennes.
     */
    private function mesurer(string $nom, callable $generateur): array
    {
        $temps = [];
        $tailles = [];

        // Tour de chauffe : évite de mesurer l'autoload et la compilation
        // Blade dans la première itération.
        $generateur();

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            gc_collect_cycles();
            $memoireAvant = memory_get_usage(true);
            $depart = microtime(true);

            $pdf = $generateur();

            $temps[] = (microtime(true) - $depart) * 1000;
            $tailles[] = strlen($pdf);
            $memoirePic = memory_get_peak_usage(true) - $memoireAvant;
        }

        return [
            'nom' => $nom,
            'tempsMoyen' => round(array_sum($temps) / count($temps)),
            'tempsMin' => round(min($temps)),
            'tempsMax' => round(max($temps)),
            'taille' => round($tailles[0] / 1024),
            'memoire' => round(max(0, $memoirePic) / 1024 / 1024, 1),
        ];
    }

    /**
     * Lance WeasyPrint sur un HTML trivial pour isoler le temps que coûte
     * la création/destruction du conteneur, indépendamment du rendu.
     */
    private function mesurerCoutDemarrageDocker(): int
    {
        $dossier = storage_path('app/weasyprint');
        File::ensureDirectoryExists($dossier);
        File::put($dossier . DIRECTORY_SEPARATOR . 'trivial.html', '<html><body>x</body></html>');

        $temps = [];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $depart = microtime(true);

            $process = new Process([
                'docker', 'run', '--rm',
                '-v', $dossier . ':/data',
                'weasyprint-local',
                '/data/trivial.html', '/data/trivial.pdf',
            ]);
            $process->setTimeout(120);
            $process->run();

            $temps[] = (microtime(true) - $depart) * 1000;
        }

        return (int) round(array_sum($temps) / count($temps));
    }

    private function genererDomPdf(array $donnees): string
    {
        return DomPdf::loadView('pdf.test-perf', $donnees)->output();
    }

    private function genererMpdf(array $donnees): string
    {
        $mpdf = new Mpdf([
            'format' => 'A4',
            'tempDir' => storage_path('app/mpdf-tmp'),
        ]);
        $mpdf->WriteHTML(view('pdf.test-perf', $donnees)->render());

        return $mpdf->Output('', 'S');
    }

    private function genererWeasyprint(array $donnees): string
    {
        $dossier = storage_path('app/weasyprint');
        File::ensureDirectoryExists($dossier);

        $html = $dossier . DIRECTORY_SEPARATOR . 'perf-source.html';
        $pdf  = $dossier . DIRECTORY_SEPARATOR . 'perf-resultat.pdf';

        File::put($html, view('pdf.test-perf', $donnees)->render());

        $process = new Process([
            'docker', 'run', '--rm',
            '-v', $dossier . ':/data',
            'weasyprint-local',
            '/data/perf-source.html', '/data/perf-resultat.pdf',
        ]);
        $process->setTimeout(120);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        return File::get($pdf);
    }

    private function donneesTest(): array
    {
        return [
            'infos' => [
                'Appareil' => 'Modèle de démonstration - FABRICANT',
                'Numéro de série' => 'DEMO-123456',
                'Réglages' => 'Mode: AutoSet, Pression: 6 - 14 (cmH2O)',
                'Masque' => 'Masque de démonstration',
                'Type' => 'Narinaire',
                'Humidificateur' => 'OUI',
                'Circuit chauffant' => 'NON',
            ],
            'indicateurs' => [
                ['libelle' => "Jours d'utilisation", 'valeur' => '28 jours', 'pourcentage' => '93%'],
                ['libelle' => '>= 4 heures', 'valeur' => '24 jours', 'pourcentage' => '80%'],
                ['libelle' => '<= 4 heures', 'valeur' => '4 jours', 'pourcentage' => '13%'],
                ['libelle' => 'Utilisation moyenne (période)', 'valeur' => '06H12', 'pourcentage' => '-'],
                ['libelle' => "Utilisation moyenne (jours d'usage)", 'valeur' => '06H40', 'pourcentage' => '-'],
                ['libelle' => 'IAH résiduel', 'valeur' => '2.8', 'pourcentage' => '-'],
                ['libelle' => 'Index apnée', 'valeur' => '1.2', 'pourcentage' => '-'],
                ['libelle' => 'Index hypopnée', 'valeur' => '1.6', 'pourcentage' => '-'],
            ],
            'graphiques' => [
                'Utilisation en heure',
                'IAH résiduel',
                'Fuites en litres par minute',
                'Fréquence moyenne en respirations par minute',
                'Inspirations spontanées en pourcentage',
                'Expirations spontanées en pourcentage',
                'Volume tidal médian en litres',
                "Différence du temps d'utilisation par rapport à la veille",
            ],
        ];
    }
}

/*
Route à ajouter dans routes/web.php :

use App\Http\Controllers\BenchmarkController;

Route::get('/benchmark', [BenchmarkController::class, 'run']);
*/