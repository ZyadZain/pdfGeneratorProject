<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Tableau de bord — démo</title>
<style>
    body { font-family: system-ui, Arial, sans-serif; max-width: 700px; margin: 40px auto; padding: 0 20px; }
    button { padding: 10px 18px; font-size: 15px; cursor: pointer; margin-top: 20px; }
    .note { background: #f4f4f4; padding: 12px; font-size: 14px; border-left: 3px solid #4F46E5; }
</style>
</head>
<body>

    <h1>Tableau de bord — Boutique Fictive TechStore</h1>

    <p class="note">
        Cette page représente le <strong>vrai front</strong> : le graphique ci-dessous
        est celui que l'utilisateur consulte normalement. Le bouton n'en crée pas
        un nouveau — il photographie celui-ci et l'envoie au backend.
    </p>

    <canvas id="graphique-ca" width="500" height="250"></canvas>

    <button id="btn-pdf">Générer le PDF</button>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        // Instance de graphique "normale" du front, avec les réglages
        // habituels d'une vraie page web (responsive, animations...).
        const monGraphique = new Chart(document.getElementById('graphique-ca'), {
            type: 'bar',
            data: {
                labels: @json(array_keys($chiffreAffairesParMois)),
                datasets: [{
                    label: "Chiffre d'affaires (€)",
                    data: @json(array_values($chiffreAffairesParMois)),
                    backgroundColor: '#4F46E5',
                }],
            },
        });

        document.getElementById('btn-pdf').addEventListener('click', async () => {
            // LE POINT CLÉ : on ne reconstruit rien, on prend une photo de
            // l'instance de graphique déjà rendue à l'écran, telle quelle.
            const imageGraphique = monGraphique.toBase64Image();

            const reponse = await fetch('/rapport-activite/depuis-front', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ imageGraphique }),
            });

            // Ouvre le PDF reçu dans un nouvel onglet.
            const blob = await reponse.blob();
            window.open(URL.createObjectURL(blob), '_blank');
        });
    </script>

</body>
</html>