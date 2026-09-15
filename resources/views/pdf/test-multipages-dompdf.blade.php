<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    /* ================================================================
       VERSION DOMPDF — mécanisme natif
       En-tête/pied : position: fixed (supporté par DomPDF en paginé)
       Pagination   : API PHP embarquée (voir <script type="text/php">)
       ================================================================ */
    @page { margin: 90px 40px 60px 40px; }

    body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #111; }

    header {
        position: fixed;
        top: -70px; left: 0; right: 0;
        height: 60px;
        border-bottom: 2px solid #4F46E5;
        padding-bottom: 5px;
    }
    header .gauche { float: left; }
    header .droite { float: right; text-align: right; }

    footer {
        position: fixed;
        bottom: -40px; left: 0; right: 0;
        height: 30px;
        border-top: 1px solid #999;
        padding-top: 5px;
        font-size: 9px;
        color: #555;
    }

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

    {{-- PAGINATION NATIVE DOMPDF.
         Nécessite 'enable_php' => true dans config/dompdf.php.
         {PAGE_NUM} et {PAGE_COUNT} sont remplacés par DomPDF au moment
         du rendu final, quand il connaît enfin le nombre total de pages
         — ce que counter(pages) en CSS n'arrivait pas à faire. --}}
    <script type="text/php">
        if (isset($pdf)) {
            $texte = "Page {PAGE_NUM} / {PAGE_COUNT}";
            $police = $fontMetrics->getFont("Helvetica", "normal");
            $taille = 9;
            $largeur = $fontMetrics->getTextWidth($texte, $police, $taille);
            // Positionnement en bas à droite (A4 = 595 x 842 points)
            $pdf->page_text(555 - $largeur, 800, $texte, $police, $taille, [0.33, 0.33, 0.33]);
        }
    </script>

    <header>
        <div class="gauche">
            <strong>Boutique Fictive TechStore</strong><br>
            12 Rue de la Simulation, 75000 Paris
        </div>
        <div class="droite">
            Responsable : J. Dupont<br>
            Période : Septembre 2026
        </div>
    </header>

    <footer>
        <span>Document généré le {{ now()->format('d/m/Y à H:i') }}</span>
    </footer>

    <main>
        <h2>Test A &amp; B — En-tête et pied de page répétés</h2>
        <p>
            L'en-tête bleu et le pied de page doivent apparaître à l'identique
            sur les 4 pages. La pagination en bas à droite est générée par
            l'API PHP de DomPDF et doit afficher le <strong>total réel</strong>
            (ex : « Page 1 / 4 »), et non « / 0 ».
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
    </main>

</body>
</html>