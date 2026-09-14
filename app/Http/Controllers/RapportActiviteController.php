<?php

namespace App\Http\Controllers;

use App\Services\RapportActiviteService;
use Barryvdh\DomPDF\Facade\Pdf;

class RapportActiviteController extends Controller {

    public function apercu(RapportActiviteService $service) {
        $donnees = $service->construireDonnees();

        $pdf = Pdf::loadView('pdf.rapport-activite', $donnees);

        return $pdf->stream('rapport-activite.pdf');
    }
}