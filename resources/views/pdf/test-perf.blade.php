<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #111; }
    h2 { font-size: 13px; margin: 12px 0 4px 0; border-left: 4px solid #4F46E5; padding-left: 8px; }
    .bandeau { background-color: #3399ff; color: #fff; padding: 4px 6px; font-weight: bold; margin: 10px 0 4px 0; }
    .ligne { border-bottom: 1px solid #ccc; padding: 2px 0; }
    .saut { page-break-before: always; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    th, td { border: 1px solid #333; padding: 3px 5px; text-align: left; }
    thead { display: table-header-group; }
    th { background-color: #e8e8f5; }
    .graphique { margin-bottom: 8px; }
    .titre-graphique { font-size: 10px; font-weight: bold; margin-bottom: 2px; }
</style>
</head>
<body>

    {{-- PAGE 1 — bloc d'informations, comme la page 1 du rapport réel --}}
    <h2>Rapport de test — page 1</h2>

    <div class="bandeau">Appareillage</div>
    @foreach ($infos as $libelle => $valeur)
        <div class="ligne"><strong>{{ $libelle }} :</strong> {{ $valeur }}</div>
    @endforeach

    <div class="bandeau">Utilisation</div>
    <table>
        <thead>
            <tr><th>Indicateur</th><th>Valeur</th><th>Pourcentage</th></tr>
        </thead>
        <tbody>
            @foreach ($indicateurs as $indicateur)
                <tr>
                    <td>{{ $indicateur['libelle'] }}</td>
                    <td>{{ $indicateur['valeur'] }}</td>
                    <td>{{ $indicateur['pourcentage'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- PAGES 2 à 4 — les 8 graphiques, répartis comme dans le rapport réel --}}
    @foreach ($graphiques as $index => $titre)
        @if ($index % 3 === 0)
            <div class="saut"></div>
            <h2>Graphiques — suite</h2>
        @endif

        <div class="graphique">
            <div class="titre-graphique">{{ $titre }}</div>
            @include('pdf._graphique-svg')
        </div>
    @endforeach

</body>
</html>