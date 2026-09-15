<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    /* ================================================================
       VERSION WEASYPRINT — CSS Paged Media standard
       Pas d'API PHP, pas de position:fixed bricolé : on utilise les
       règles @page du W3C, ce pour quoi WeasyPrint est conçu.
       ================================================================ */
    @page {
        size: A4;
        margin: 30mm 15mm 20mm 15mm;

        /* En-tête : zones de marge nommées, remplies via content */
        @top-left {
            content: "Boutique Fictive TechStore\A 12 Rue de la Simulation, 75000 Paris";
            white-space: pre;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9pt;
            font-weight: bold;
            vertical-align: bottom;
            padding-bottom: 3mm;
        }
        @top-right {
            content: "Responsable : J. Dupont\A Période : Septembre 2026";
            white-space: pre;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9pt;
            text-align: right;
            vertical-align: bottom;
            padding-bottom: 3mm;
        }

        /* Pied de page + PAGINATION : compteurs CSS natifs.
           C'est précisément ce que DomPDF ne savait pas faire. */
        @bottom-left {
            content: "Document généré le {{ now()->format('d/m/Y à H:i') }}";
            font-family: Helvetica, Arial, sans-serif;
            font-size: 8pt;
            color: #555;
            vertical-align: top;
            padding-top: 2mm;
        }
        @bottom-right {
            content: "Page " counter(page) " / " counter(pages);
            font-family: Helvetica, Arial, sans-serif;
            font-size: 8pt;
            color: #555;
            vertical-align: top;
            padding-top: 2mm;
        }
    }

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

    /* TEST BONUS — CSS moderne, impossible avec DomPDF et mPDF.
       Si ces trois blocs s'affichent côte à côte, c'est que flexbox
       fonctionne réellement. */
    .cartes { display: flex; gap: 10px; margin: 15px 0; }
    .carte {
        flex: 1;
        border: 1px solid #4F46E5;
        border-radius: 6px;
        padding: 10px;
        background: #f5f5ff;
    }
    .carte strong { display: block; font-size: 16px; color: #4F46E5; }
</style>
</head>
<body>

    <h2>Test A &amp; B — En-tête et pied de page répétés</h2>
    <p>
        L'en-tête et le pied sont définis par les règles <code>@page</code> du
        CSS standard. La pagination utilise <code>counter(page)</code> et
        <code>counter(pages)</code> — exactement ce qui échouait avec DomPDF.
    </p>

    <h2>Test bonus — Flexbox (impossible avec DomPDF et mPDF)</h2>
    <div class="cartes">
        <div class="carte"><strong>1 660 €</strong>Chiffre d'affaires</div>
        <div class="carte"><strong>50</strong>Articles vendus</div>
        <div class="carte"><strong>4</strong>Références</div>
    </div>

    <h2>Test E — Tableau long coupé entre deux pages</h2>
    <table>
        <thead>
            <tr><th>#</th><th>Produit</th><th>Quantité</th><th>Prix unitaire</th></tr>
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