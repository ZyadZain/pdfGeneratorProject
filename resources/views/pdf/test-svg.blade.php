<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #111; }
    h2 { font-size: 13px; margin: 15px 0 5px 0; border-left: 4px solid #4F46E5; padding-left: 8px; }
    .encadre { border: 1px solid #ccc; padding: 8px; margin-bottom: 15px; }
    .note { font-size: 9px; color: #777; margin-top: 4px; }
</style>
</head>
<body>

    <h1 style="font-size: 16px;">Test d'intégration SVG</h1>
    <p>
        Deux modes d'insertion du même graphique. Le résultat attendu : une
        courbe bleue avec zone remplie, points blancs cerclés, grille grise,
        seuil rouge en pointillés, et tous les libellés lisibles.
    </p>

    <h2>Mode 1 — SVG inline (balise &lt;svg&gt; dans le HTML)</h2>
    <div class="encadre">
        @include('pdf._graphique-svg')
        <div class="note">
            C'est ce que produirait un rendu serveur de Recharts :
            le SVG est injecté directement dans le document.
        </div>
    </div>

    <h2>Mode 2 — SVG en data URI (&lt;img src="data:image/svg+xml;base64,..."&gt;)</h2>
    <div class="encadre">
        @php
            $svgBrut = view('pdf._graphique-svg')->render();
            $dataUri = 'data:image/svg+xml;base64,' . base64_encode($svgBrut);
        @endphp
        <img src="{{ $dataUri }}" width="450" height="220" alt="Graphique">
        <div class="note">
            Même SVG, encodé en base64 dans une balise img. Certains moteurs
            gèrent mieux ce mode que le SVG inline — ou l'inverse.
        </div>
    </div>

    <h2>Points de contrôle</h2>
    <ul>
        <li>La zone bleue sous la courbe est-elle translucide (fill-opacity) ?</li>
        <li>Le seuil rouge est-il bien en pointillés (stroke-dasharray) ?</li>
        <li>Les libellés des axes sont-ils tous présents et lisibles ?</li>
        <li>Les points blancs sont-ils cerclés de bleu ?</li>
        <li>Les deux modes donnent-ils le même résultat ?</li>
    </ul>

</body>
</html>