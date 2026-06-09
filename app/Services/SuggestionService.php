<?php

namespace App\Services;

use App\Models\Offre;
use App\Models\Particulier;

class SuggestionService
{
    /**
     * Suggérer des offres à un candidat selon ses compétences
     */
    public static function offresParCompetences(Particulier $particulier, int $limit = 6): \Illuminate\Support\Collection
    {
        $competenceIds = $particulier->competances->pluck('id');

        if ($competenceIds->isEmpty()) {
            return collect();
        }

        // Récupérer les offres actives qui ont au moins une compétence en commun
        $offres = Offre::active()
            ->with(['entreprise', 'categorie', 'competances'])
            ->whereHas('competances', fn($q) => $q->whereIn('competances.id', $competenceIds))
            ->whereNotIn('id', $particulier->candidatures->pluck('offre_id'))
            ->get();

        // Calculer le score pour chaque offre
        $scored = $offres->map(function ($offre) use ($competenceIds, $particulier) {
            $offreCompIds = $offre->competances->pluck('id');
            $matched      = $particulier->competances
                                ->whereIn('id', $offreCompIds->toArray())
                                ->pluck('nom')
                                ->toArray();

            return [
                'offre'   => $offre,
                'score'   => count($matched),
                'matched' => $matched,
            ];
        });

        return $scored
            ->filter(fn($item) => $item['score'] > 0)
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    /**
     * Suggérer des candidats pour une offre
     */
    public static function candidatsParOffre(Offre $offre, int $limit = 8): \Illuminate\Support\Collection
    {
        $offreCompIds = $offre->competances->pluck('id');

        if ($offreCompIds->isEmpty()) {
            return collect();
        }

        // Récupérer les candidats qui ont au moins une compétence en commun
        $particuliers = Particulier::with(['utilisateur', 'competances', 'cv'])
            ->whereHas('competances', fn($q) => $q->whereIn('competances.id', $offreCompIds))
            ->get();

        $scored = $particuliers->map(function ($particulier) use ($offreCompIds) {
            $matched = $particulier->competances
                ->whereIn('id', $offreCompIds->toArray())
                ->pluck('nom')
                ->toArray();

            return [
                'particulier' => $particulier,
                'score'       => count($matched),
                'matched'     => $matched,
            ];
        });

        return $scored
            ->filter(fn($item) => $item['score'] > 0)
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }
}