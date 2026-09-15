<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Benchmark — moteurs PDF</title>
<style>
    body { font-family: system-ui, Arial, sans-serif; max-width: 900px; margin: 40px auto; padding: 0 20px; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    th { background: #f4f4f8; }
    td.nombre { text-align: right; font-variant-numeric: tabular-nums; }
    .note { background: #f4f4f4; padding: 12px; font-size: 14px; border-left: 3px solid #4F46E5; margin: 20px 0; }
    .corrige { color: #1a7f37; font-weight: bold; }
</style>
</head>
<body>

    <h1>Benchmark des moteurs PDF</h1>
    <p>Document de test : 4 pages, 8 graphiques SVG, 1 tableau, blocs d'informations.
       Moyenne sur {{ $iterations }} itérations, après un tour de chauffe.</p>

    <table>
        <thead>
            <tr>
                <th>Moteur</th>
                <th>Temps moyen</th>
                <th>Min / Max</th>
                <th>Mémoire PHP</th>
                <th>Taille PDF</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($resultats as $r)
                <tr>
                    <td>{{ $r['nom'] }}</td>
                    <td class="nombre">{{ number_format($r['tempsMoyen']) }} ms</td>
                    <td class="nombre">{{ number_format($r['tempsMin']) }} / {{ number_format($r['tempsMax']) }} ms</td>
                    <td class="nombre">{{ $r['memoire'] }} Mo</td>
                    <td class="nombre">{{ number_format($r['taille']) }} Ko</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $weasy = collect($resultats)->firstWhere('nom', 'WeasyPrint (Docker)');
        $tempsCorrige = $weasy ? max(0, $weasy['tempsMoyen'] - $coutDemarrage) : null;
    @endphp

    <div class="note">
        <strong>Correction du coût Docker.</strong><br>
        Le démarrage et la destruction d'un conteneur coûtent à eux seuls
        <strong>{{ number_format($coutDemarrage) }} ms</strong> (mesuré sur un document trivial).
        Cette surcharge est un artefact du poste de développement Windows : en production,
        WeasyPrint sera installé dans l'image applicative et appelé directement.<br><br>
        @if ($tempsCorrige !== null)
            Temps de rendu réel estimé pour WeasyPrint :
            <span class="corrige">≈ {{ number_format($tempsCorrige) }} ms</span>
        @endif
    </div>

    <h2>Comment lire ces chiffres</h2>
    <ul>
        <li><strong>Temps</strong> — pour une génération déclenchée par un clic utilisateur,
            tout ce qui reste sous ~2 s est confortable.</li>
        <li><strong>Mémoire PHP</strong> — critère clé à forte charge : elle est consommée
            par le worker PHP-FPM qui traite la requête. WeasyPrint affiche peu ici car
            le rendu se passe hors du process PHP (sa mémoire est consommée ailleurs).</li>
        <li><strong>Taille</strong> — un PDF plus léger se télécharge et s'archive mieux.
            Un écart notable révèle une différence de traitement des polices ou des images.</li>
    </ul>

</body>
</html>