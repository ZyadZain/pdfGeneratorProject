<?php

namespace App\Services;

class RapportActiviteService
{
    /**
     * Construit les données du rapport pour une boutique et un mois donnés.
     *
     * Pour ce projet fictif, les données sont codées en dur.
     * Dans le vrai projet (IGEIA-CORETEX), cette méthode ferait plutôt :
     *   Visite::with(['patient', 'technicien', 'materielVisites.materiel'])->findOrFail($id)
     * puis mapperait les relations Eloquent vers ce même genre de tableau/DTO,
     * au lieu du gros SQL brut du code legacy.
     */
    public function construireDonnees(): array
    {
        $produitsVendus = [
            ['nom' => 'Casque Bluetooth', 'quantite' => 12, 'prixUnitaire' => 39.90],
            ['nom' => 'Chargeur USB-C 65W', 'quantite' => 25, 'prixUnitaire' => 24.90],
            ['nom' => 'Souris sans fil', 'quantite' => 8, 'prixUnitaire' => 19.90],
            ['nom' => 'Clavier mécanique', 'quantite' => 5, 'prixUnitaire' => 79.90],
        ];

        foreach ($produitsVendus as &$produit) {
            $produit['total'] = round($produit['quantite'] * $produit['prixUnitaire'], 2);
        }
        unset($produit);

        $chiffreAffairesParMois = [
            'Avril' => 1250,
            'Mai' => 1480,
            'Juin' => 1390,
            'Juillet' => 1620,
            'Aout' => 1510,
            'Septembre' => 1780,
        ];

        return [
            'boutique' => [
                'nom' => 'Boutique Fictive TechStore',
                'adresse' => '12 Rue de la Simulation, 75000 Paris',
                'responsable' => 'J. Dupont',
            ],
            'periode' => 'Septembre 2026',
            'produitsVendus' => $produitsVendus,
            'chiffreAffairesTotal' => round(array_sum(array_column($produitsVendus, 'total')), 2),
            'commentaire' => "Bonne dynamique sur les accessoires audio ce mois-ci, hausse portée par une opération commerciale.",
            'urlGraphique' => $this->construireUrlGraphique($chiffreAffairesParMois),
            // Données brutes, en plus de l'image ci-dessus : utilisées par la
            // version "Chart.js réel" du rapport (test Browsershot), qui
            // dessine le graphique elle-même au lieu de charger une image.
            'chiffreAffairesParMois' => $chiffreAffairesParMois,
        ];
    }

    /**
     * Récupère le graphique QuickChart et le renvoie en data URI base64.
     *
     * Pourquoi ne pas laisser DomPDF charger l'URL distante directement :
     * DomPDF ne décode pas correctement les "&" échappés en "&amp;" par
     * Blade dans l'attribut src, ce qui casse l'URL au moment du fetch.
     * En téléchargeant l'image nous-mêmes et en l'embarquant en base64,
     * on élimine ce problème ET on se rapproche du pattern qu'il faudra de
     * toute façon utiliser en prod avec une instance QuickChart
     * auto-hébergée (aucune donnée patient ne doit sortir vers un service
     * tiers pour les vraies données de santé — observance, IAH, etc.).
     */
    private function construireUrlGraphique(array $donneesParMois): string
    {
        $config = [
            'type' => 'bar',
            'data' => [
                'labels' => array_keys($donneesParMois),
                'datasets' => [[
                    'label' => "Chiffre d'affaires (€)",
                    'data' => array_values($donneesParMois),
                    'backgroundColor' => '#4F46E5',
                ]],
            ],
        ];

        $url = 'https://quickchart.io/chart?width=500&height=250&c=' . urlencode(json_encode($config));

        $imageData = file_get_contents($url);

        if ($imageData === false) {
            // En prod, logguer l'erreur plutôt que de planter tout le PDF.
            return '';
        }

        return 'data:image/png;base64,' . base64_encode($imageData);
    }
}