<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    /* ================================================================
       TEST MULTI-PAGES
       Techniques CSS "Paged Media" — le standard pour l'impression.
       Chaque moteur les supporte plus ou moins bien : c'est justement
       ce qu'on mesure ici.
       ================================================================ */

    @page {
        margin: 90px 40px 60px 40px; /* place réservée à l'en-tête et au pied */
    }

    body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #111; }

    /* TEST A — En-tête répété automatiquement sur CHAQUE page.
       position: fixed dans un contexte paginé = répétition sur toutes
       les pages. C'est LE point qui évite de redupliquer le bloc à la
       main comme le fait le code FPDF actuel. */
    header {
        position: fixed;
        top: -70px; left: 0; right: 0;
        height: 60px;
        border-bottom: 2px solid #4F46E5;
        padding-bottom: 5px;
    }
    header .gauche { float: left; }
    header .droite { float: right; text-align: right; }

    /* TEST B — Pied de page répété + PAGINATION AUTOMATIQUE.
       Les compteurs CSS "page" et "pages" sont générés par le moteur.
       Si le moteur ne les supporte pas, on verra des valeurs vides ou
       littérales au lieu des numéros. */
    footer {
        position: fixed;
        bottom: -40px; left: 0; right: 0;
        height: 30px;
        border-top: 1px solid #999;
        padding-top: 5px;
        font-size: 9px;
        color: #555;
    }
    footer .pagination { float: right; }
    footer .pagination:after {
        content: "Page " counter(page) " / " counter(pages);
    }

    /* TEST C — Saut de page explicite */
    .saut-de-page { page-break-before: always; }

    /* TEST D — Bloc insécable : ne doit JAMAIS être coupé entre 2 pages */
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
    /* TEST E — En-tête de tableau répété si le tableau se coupe sur
       plusieurs pages (comportement natif de <thead> en paged media) */
    thead { display: table-header-group; }
    th { background-color: #e8e8f5; }
</style>
</head>
<body>

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
        <span class="pagination"></span>
    </footer>

    <main>

        <h2>Test A &amp; B — En-tête et pied de page répétés</h2>
        <p>
            L'en-tête bleu en haut et le pied de page en bas doivent apparaître
            <strong>à l'identique sur les 4 pages</strong>, sans avoir été écrits
            4 fois. La pagination en bas à droite doit afficher le numéro de page
            courant et le total (ex : « Page 1 / 4 »), calculés par le moteur.
        </p>

        <h2>Test E — Tableau long coupé entre deux pages</h2>
        <p>
            Ce tableau de 40 lignes va forcément déborder sur la page suivante.
            L'en-tête de colonnes (ligne grise) doit se <strong>répéter en haut
            de chaque page</strong> où le tableau continue.
        </p>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
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
        <p>
            Ce titre doit se trouver <strong>en haut d'une nouvelle page</strong>,
            provoqué par <code>page-break-before: always</code>.
        </p>

        {{-- On pousse le contenu pour que le bloc insécable ci-dessous
             tombe pile à la limite d'une page — c'est là que le test D
             devient réellement discriminant. --}}
        @for ($i = 1; $i <= 18; $i++)
            <p>Ligne de remplissage n°{{ $i }} destinée à pousser le bloc rouge vers la coupure de page.</p>
        @endfor

        <div class="insecable">
            <strong>Test D — Bloc insécable</strong><br>
            Ce bloc rouge tombe naturellement à cheval sur une coupure de page.
            Grâce à <code>page-break-inside: avoid</code>, il doit être déplacé
            <strong>entièrement sur la page suivante</strong> plutôt que d'être
            coupé en deux. Si vous voyez ce cadre scindé entre deux pages,
            le moteur ne supporte pas cette propriété.<br><br>
            Ligne supplémentaire pour donner de la hauteur au bloc.<br>
            Ligne supplémentaire pour donner de la hauteur au bloc.<br>
            Ligne supplémentaire pour donner de la hauteur au bloc.
        </div>

        <div class="saut-de-page"></div>

        <h2>Page finale</h2>
        <p>
            Dernière page du document. Vérifiez que le total affiché dans la
            pagination correspond bien au nombre réel de pages générées.
        </p>

    </main>

</body>
</html>