<?php

namespace App\Services;

use App\Models\Offre;
use App\Models\Particulier;

class MatchingService
{
    /**
     * Calculer le score de matching entre un particulier et une offre
     */
    public static function calculer(Particulier $particulier, Offre $offre): array
    {
        $criteres = [];

        // ── 1. Compétences (60 points) ───────────────────────────────
        $criteres['competences'] = self::scoreCompetences($particulier, $offre);

        // ── 2. Localisation (20 points) ──────────────────────────────
        $criteres['localisation'] = self::scoreLocalisation($particulier, $offre);

        // ── 3. Profil complété (20 points) ───────────────────────────
        $criteres['profil'] = self::scoreProfil($particulier);

        $total = array_sum(array_column($criteres, 'score'));

        return [
            'score'    => min(100, $total),
            'niveau'   => self::niveau($total),
            'couleur'  => self::couleur($total),
            'criteres' => $criteres,
        ];
    }

    /**
     * Calculer le matching pour une liste d'offres — triées par score
     */
    public static function offresPourCandidат(Particulier $particulier, $offres): \Illuminate\Support\Collection
    {
        return $offres->map(function ($offre) use ($particulier) {
            // Charger les compétences si pas déjà chargées
            if (!$offre->relationLoaded('competances')) {
                $offre->load('competances');
            }
            $matching        = self::calculer($particulier, $offre);
            $offre->matching = $matching;
            return $offre;
        })
        ->filter(fn($o) => $o->matching['score'] > 0)
        ->sortByDesc(fn($o) => $o->matching['score'])
        ->values();
    }

    /**
     * Calculer le matching pour une liste de candidats — triés par score
     */
    public static function candidatsPourOffre(Offre $offre, $particuliers): \Illuminate\Support\Collection
    {
        return $particuliers->map(function ($particulier) use ($offre) {
            if (!$particulier->relationLoaded('competances')) {
                $particulier->load('competances');
            }
            $matching              = self::calculer($particulier, $offre);
            $particulier->matching = $matching;
            return $particulier;
        })
        ->filter(fn($p) => $p->matching['score'] > 0)
        ->sortByDesc(fn($p) => $p->matching['score'])
        ->values();
    }

    // ── Critères ─────────────────────────────────────────────────────

    private static function scoreCompetences(Particulier $particulier, Offre $offre): array
    {
        $maxPoints = 60;

        // Compétences requises par l'offre (via table pivot)
        $offreCompIds   = $offre->competances->pluck('id');
        $candidatCompIds = $particulier->competances->pluck('id');

        if ($offreCompIds->isEmpty()) {
            return [
                'label'  => 'Compétences',
                'score'  => 20, // score neutre si l'offre n'a pas de compétences définies
                'max'    => $maxPoints,
                'detail' => [],
                'note'   => 'Aucune compétence requise définie',
                'icone'  => 'fa-star',
            ];
        }

        // Compétences en commun
        $matched     = $particulier->competances->whereIn('id', $offreCompIds->toArray());
        $matchedNoms = $matched->pluck('nom')->toArray();
        $nbMatch     = count($matchedNoms);
        $nbRequis    = $offreCompIds->count();

        // Score proportionnel : nb matchés / nb requis * maxPoints
        $score = min($maxPoints, (int) round(($nbMatch / $nbRequis) * $maxPoints));

        return [
            'label'  => 'Compétences',
            'score'  => $score,
            'max'    => $maxPoints,
            'detail' => $matchedNoms,
            'note'   => "{$nbMatch} / {$nbRequis} compétences requises",
            'icone'  => 'fa-star',
        ];
    }

    private static function scoreLocalisation(Particulier $particulier, Offre $offre): array
    {
        $maxPoints = 20;

        if (!$offre->localisation) {
            return ['label' => 'Localisation', 'score' => 10, 'max' => $maxPoints, 'detail' => [], 'note' => 'Non précisée', 'icone' => 'fa-map-marker-alt'];
        }

        $adresseOffre = strtolower($offre->localisation);

        if (str_contains($adresseOffre, 'remote')) {
            return ['label' => 'Localisation', 'score' => $maxPoints, 'max' => $maxPoints, 'detail' => [], 'note' => 'Remote — compatible partout', 'icone' => 'fa-map-marker-alt'];
        }

        if ($particulier->adresse) {
            $adresseCandidат = strtolower($particulier->adresse);
            $villeOffre      = explode(',', $adresseOffre)[0];

            if (str_contains($adresseCandidат, $villeOffre) || str_contains($adresseOffre, explode(',', $adresseCandidат)[0])) {
                return ['label' => 'Localisation', 'score' => $maxPoints, 'max' => $maxPoints, 'detail' => [], 'note' => 'Même ville ✓', 'icone' => 'fa-map-marker-alt'];
            }

            return ['label' => 'Localisation', 'score' => 8, 'max' => $maxPoints, 'detail' => [], 'note' => 'Ville différente', 'icone' => 'fa-map-marker-alt'];
        }

        return ['label' => 'Localisation', 'score' => 5, 'max' => $maxPoints, 'detail' => [], 'note' => 'Adresse non renseignée', 'icone' => 'fa-map-marker-alt'];
    }

    private static function scoreProfil(Particulier $particulier): array
    {
        $maxPoints = 20;
        $score     = 0;

        if (!empty($particulier->bio))              $score += 4;
        if (!empty($particulier->tel))              $score += 3;
        if (!empty($particulier->adresse))          $score += 3;
        if ($particulier->cv->count() > 0)          $score += 6;
        if ($particulier->competances->count() > 0) $score += 4;

        return [
            'label'  => 'Profil complété',
            'score'  => $score,
            'max'    => $maxPoints,
            'detail' => [],
            'note'   => $score === $maxPoints ? 'Profil complet ✓' : "{$score}/{$maxPoints} points",
            'icone'  => 'fa-user-check',
        ];
    }

    private static function niveau(int $score): string
    {
        return match(true) {
            $score >= 80 => 'Excellent',
            $score >= 60 => 'Bon',
            $score >= 40 => 'Moyen',
            $score >= 20 => 'Faible',
            default      => 'Très faible',
        };
    }

    private static function couleur(int $score): string
    {
        return match(true) {
            $score >= 80 => '#28a745',
            $score >= 60 => '#17a2b8',
            $score >= 40 => '#ffc107',
            $score >= 20 => '#fd7e14',
            default      => '#dc3545',
        };
    }
}