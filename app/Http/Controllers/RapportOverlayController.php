<?php

namespace App\Http\Controllers;

use setasign\Fpdi\Tcpdf\Fpdi;

class RapportOverlayController extends Controller
{
    public function apercu()
    {
        // Le PDF "existant" qu'on veut modifier — dans le vrai projet, ce
        // serait par exemple un rapport déjà archivé qu'on veut tamponner
        // "DUPLICATA" ou compléter après coup.
        $cheminSource = storage_path('app/pdfs/rapport-source.pdf');

        $pdf = new Fpdi();

        $nbPages = $pdf->setSourceFile($cheminSource);

        for ($i = 1; $i <= $nbPages; $i++) {
            // Importe la page telle quelle, à l'identique (texte, tableaux,
            // graphique déjà présents sur le PDF source ne sont PAS régénérés).
            $templateId = $pdf->importPage($i);
            $taille = $pdf->getTemplateSize($templateId);

            $pdf->AddPage(
                $taille['orientation'],
                [$taille['width'], $taille['height']]
            );
            $pdf->useTemplate($templateId);

            // Démo de superposition : un tampon "VALIDÉ" en haut à droite,
            // uniquement sur la première page.
            if ($i === 1) {
                $pdf->SetFont('helvetica', 'B', 16);
                $pdf->SetTextColor(200, 0, 0);
                $pdf->SetXY($taille['width'] - 50, 15);
                $pdf->Write(0, 'VALIDÉ');
            }
        }

        return response($pdf->Output('rapport-modifie.pdf', 'S'))
            ->header('Content-Type', 'application/pdf');
    }
}

/*
Route à ajouter dans routes/web.php :

use App\Http\Controllers\RapportOverlayController;

Route::get('/rapport-activite/overlay', [RapportOverlayController::class, 'apercu']);
*/