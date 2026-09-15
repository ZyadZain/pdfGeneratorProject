<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    /* ================================================================
       VERSION MPDF — mécanisme natif
       En-tête/pied : PAS de position:fixed ici. mPDF les reçoit via
       SetHTMLHeader() / SetHTMLFooter() côté PHP (voir le controller),
       avec ses variables natives {PAGENO} et {nbpg} pour la pagination.
       ================================================================ */
    body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #111; }

    .saut-de-page { page-break-before: always; }

    .insecable {
        page-break-inside: avoid;
        border: 2px solid #c0392b;
        padding: 10px;
        margin: 10px 0;
        background: #fdf2f2;
    }

    h2 { font-size: 14px; margin-top: 20px; border-left: 4px solid #4F46E5; padding-left: 8px; }

    table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
    thead { display: table-header-group; }
    th { background-color: #e8e8f5; }
</style>
</head>
<body>

    <h2>Test A &amp; B — En-tête et pied de page répétés</h2>
    <p>
        L'en-tête bleu et le pied de page sont injectés par l'API native de
        mPDF et doivent apparaître à l'identique sur les 4 pages, avec une
        pagination « Page X / Y » complète en bas à droite.
    </p>

    <h2>Test E — Tableau long coupé entre deux pages</h2>
    <table>
        <thead>
            <tr>
                <th>#</th><th>Produit</th><th>Quantité</th><th>Prix unitaire</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 1; $i <= 40; $i++)
                <tr>
                    <td>{{ $i }}</td>
                    <td>Article de démonstration n°{{ $i }}</td>
                    <td>{{ $i * 3 }}</td>
                    <td>{{ number_format($i * 7.5, 2) }} €</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="saut-de-page"></div>

    <h2>Test C — Saut de page explicite</h2>
    <p>Ce titre doit être en haut d'une nouvelle page.</p>

    @for ($i = 1; $i <= 18; $i++)
        <p>Ligne de remplissage n°{{ $i }} destinée à pousser le bloc rouge vers la coupure de page.</p>
    @endfor

    <div class="insecable">
        <strong>Test D — Bloc insécable</strong><br>
        Ce bloc doit être déplacé entièrement sur la page suivante plutôt
        que d'être coupé en deux.<br><br>
        Ligne supplémentaire pour donner de la hauteur au bloc.<br>
        Ligne supplémentaire pour donner de la hauteur au bloc.<br>
        Ligne supplémentaire pour donner de la hauteur au bloc.
    </div>

    <div class="saut-de-page"></div>

    <h2>Page finale</h2>
    <p>Vérifiez que le total de la pagination correspond au nombre réel de pages.</p>

</body>
</html>