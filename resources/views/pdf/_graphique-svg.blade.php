{{--
    SVG de test — reproduit la structure typique d'un graphique Recharts :
    grille de fond, axes avec graduations, libellés texte, courbe avec
    points, zone remplie sous la courbe, ligne de seuil en pointillés.

    C'est volontairement le genre de SVG que Recharts produit réellement :
    des <path>, <line>, <text>, <circle>, avec des attributs de style
    (stroke-dasharray, fill-opacity, text-anchor).
--}}
<svg width="450" height="220" viewBox="0 0 450 220" xmlns="http://www.w3.org/2000/svg">

    <!-- Grille horizontale -->
    <g stroke="#e0e0e0" stroke-width="1">
        <line x1="50" y1="20"  x2="430" y2="20"/>
        <line x1="50" y1="60"  x2="430" y2="60"/>
        <line x1="50" y1="100" x2="430" y2="100"/>
        <line x1="50" y1="140" x2="430" y2="140"/>
        <line x1="50" y1="180" x2="430" y2="180"/>
    </g>

    <!-- Axes -->
    <line x1="50" y1="20" x2="50" y2="180" stroke="#333" stroke-width="1.5"/>
    <line x1="50" y1="180" x2="430" y2="180" stroke="#333" stroke-width="1.5"/>

    <!-- Graduations axe Y -->
    <g font-family="Helvetica, Arial, sans-serif" font-size="9" fill="#555" text-anchor="end">
        <text x="45" y="24">2000</text>
        <text x="45" y="64">1500</text>
        <text x="45" y="104">1000</text>
        <text x="45" y="144">500</text>
        <text x="45" y="184">0</text>
    </g>

    <!-- Zone remplie sous la courbe (fill-opacity : souvent mal géré) -->
    <path d="M 75,80 L 135,56 L 195,63 L 255,44 L 315,52 L 375,31 L 375,180 L 75,180 Z"
          fill="#4F46E5" fill-opacity="0.15"/>

    <!-- Courbe -->
    <path d="M 75,80 L 135,56 L 195,63 L 255,44 L 315,52 L 375,31"
          fill="none" stroke="#4F46E5" stroke-width="2.5"
          stroke-linecap="round" stroke-linejoin="round"/>

    <!-- Points -->
    <g fill="#ffffff" stroke="#4F46E5" stroke-width="2">
        <circle cx="75"  cy="80" r="4"/>
        <circle cx="135" cy="56" r="4"/>
        <circle cx="195" cy="63" r="4"/>
        <circle cx="255" cy="44" r="4"/>
        <circle cx="315" cy="52" r="4"/>
        <circle cx="375" cy="31" r="4"/>
    </g>

    <!-- Ligne de seuil en pointillés (comme les seuils d'observance) -->
    <line x1="50" y1="100" x2="430" y2="100"
          stroke="#c0392b" stroke-width="1.5" stroke-dasharray="6,4"/>
    <text x="428" y="96" font-family="Helvetica, Arial, sans-serif"
          font-size="9" fill="#c0392b" text-anchor="end">Seuil</text>

    <!-- Libellés axe X -->
    <g font-family="Helvetica, Arial, sans-serif" font-size="9" fill="#555" text-anchor="middle">
        <text x="75"  y="195">Avril</text>
        <text x="135" y="195">Mai</text>
        <text x="195" y="195">Juin</text>
        <text x="255" y="195">Juillet</text>
        <text x="315" y="195">Aout</text>
        <text x="375" y="195">Septembre</text>
    </g>

</svg>