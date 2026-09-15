<?php

namespace App\Http\Controllers;

use App\Services\RapportActiviteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RapportActiviteFrontImageController extends Controller
{
    /**
     * Scénario réel : l'utilisateur consulte une page contenant les données
     * ET les graphiques déjà rendus. Il clique sur "Générer le PDF".
     * Le front appelle chart.toBase64Image() sur les instances de graphique
     * DÉJÀ affichées à l'écran, et envoie ces images au backend.
     *
     * Le backend n'exécute AUCUN JavaScript et ne reconstruit AUCUN
     * graphique : il reçoit des images et les place dans le document.
     * C'est pour ça qu'un moteur pur PHP (DomPDF) suffit largement ici.
     */
    public function generer(Request $request, RapportActiviteService $service)
    {
        $valide = $request->validate([
            // Data URI complète envoyée par le front, ex :
            // "data:image/png;base64,iVBORw0KG..."
            'imageGraphique' => ['required', 'string', 'starts_with:data:image/'],
        ]);

        $donnees = $service->construireDonnees();

        // On écrase l'image générée côté backend (QuickChart) par celle
        // envoyée par le front : c'est la photo du VRAI graphique affiché.
        $donnees['urlGraphique'] = $valide['imageGraphique'];

        $pdf = Pdf::loadView('pdf.rapport-activite', $donnees);

        return $pdf->stream('rapport-activite.pdf');
    }
}

/*
Route à ajouter dans routes/web.php :

use App\Http\Controllers\RapportActiviteFrontImageController;

Route::post('/rapport-activite/depuis-front', [RapportActiviteFrontImageController::class, 'generer'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]); // uniquement pour ce test
*/