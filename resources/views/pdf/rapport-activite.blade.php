<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    /* DomPDF ne supporte qu'un sous-ensemble de CSS : on reste sur des
       display:table / border classiques, l'équivalent moderne des
       Rect() dessinés à la main dans le code FPDF legacy. */
    body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #111; }
    .en-tete { display: table; width: 100%; margin-bottom: 15px; }
    .en-tete .col { display: table-cell; vertical-align: top; width: 50%; }
    .titre { font-size: 16px; font-weight: bold; text-align: center; margin: 15px 0; }
    .cadre { border: 1px solid #000; padding: 6px; margin-bottom: 10px; }
    table.donnees { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    table.donnees th, table.donnees td { border: 1px solid #000; padding: 4px 6px; text-align: left; }
    table.donnees th { background-color: #f0f0f0; }
    .graphique { text-align: center; margin: 15px 0; }
    .pied { font-size: 8px; color: #555; margin-top: 20px; }
</style>
</head>
<body>

    <div class="en-tete">
        <div class="col">
            <strong>{{ $boutique['nom'] }}</strong><br>
            {{ $boutique['adresse'] }}
        </div>
        <div class="col" style="text-align: right;">
            Responsable : {{ $boutique['responsable'] }}<br>
            Période : {{ $periode }}
        </div>
    </div>

    <div class="titre">RAPPORT MENSUEL D'ACTIVITÉ</div>

    <table class="donnees">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($produitsVendus as $produit)
                <tr>
                    <td>{{ $produit['nom'] }}</td>
                    <td>{{ $produit['quantite'] }}</td>
                    <td>{{ number_format($produit['prixUnitaire'], 2) }} €</td>
                    <td>{{ number_format($produit['total'], 2) }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="cadre">
        <strong>Chiffre d'affaires total : {{ number_format($chiffreAffairesTotal, 2) }} €</strong>
    </div>

    <div class="graphique">
        <strong>Évolution du chiffre d'affaires (6 derniers mois)</strong><br>
        <img src="{!! $urlGraphique !!}" width="400">
    </div>

    <div class="cadre">
        <strong>Commentaire :</strong> {{ $commentaire }}
    </div>

    <div class="pied">
        Document généré le {{ now()->format('d/m/Y à H:i') }}
    </div>

</body>
</html>