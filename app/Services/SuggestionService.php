<?php

namespace App\Services;

use App\Models\Offre;
use App\Models\Particulier;

class SuggestionService
{
    /**
     * Suggere des offres a un candidat.
     * Si un CV lisible existe, on utilise le texte du CV. Sinon, on garde la methode par competences.
     */
    public static function offresParCompetences(Particulier $particulier, int $limit = 6): \Illuminate\Support\Collection
    {
        if (self::aCvLisible($particulier)) {
            $offres = Offre::active()
                ->with(['entreprise', 'categorie', 'competances'])
                ->whereNotIn('id', $particulier->candidatures->pluck('offre_id'))
                ->get();

            return $offres->map(function ($offre) use ($particulier) {
                $matching = MatchingService::calculer($particulier, $offre);

                return [
                    'offre'   => $offre,
                    'score'   => $matching['score'],
                    'matched' => $matching['criteres']['competences']['detail'] ?? [],
                    'source'  => 'cv',
                ];
            })
                ->filter(fn($item) => $item['score'] > 0)
                ->sortByDesc('score')
                ->take($limit)
                ->values();
        }

        $competenceIds = $particulier->competances->pluck('id');

        if ($competenceIds->isEmpty()) {
            return collect();
        }

        $offres = Offre::active()
            ->with(['entreprise', 'categorie', 'competances'])
            ->whereHas('competances', fn($q) => $q->whereIn('competances.id', $competenceIds))
            ->whereNotIn('id', $particulier->candidatures->pluck('offre_id'))
            ->get();

        $scored = $offres->map(function ($offre) use ($particulier) {
            $offreCompIds = $offre->competances->pluck('id');
            $matched = $particulier->competances
                ->whereIn('id', $offreCompIds->toArray())
                ->pluck('nom')
                ->toArray();

            return [
                'offre'   => $offre,
                'score'   => count($matched),
                'matched' => $matched,
                'source'  => 'profil',
            ];
        });

        return $scored
            ->filter(fn($item) => $item['score'] > 0)
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    /**
     * Suggere des candidats pour une offre.
     * Si le candidat a un CV lisible, on score via CV. Sinon, on garde les competences saisies.
     */
    public static function candidatsParOffre(Offre $offre, int $limit = 8): \Illuminate\Support\Collection
    {
        if (!$offre->relationLoaded('competances')) {
            $offre->load('competances');
        }

        $offreCompIds = $offre->competances->pluck('id');

        $particuliers = Particulier::with(['utilisateur', 'competances', 'cv'])
            ->whereHas('utilisateur', fn($q) => $q->where('role', 'particulier'))
            ->when($offreCompIds->isNotEmpty(), function ($query) use ($offreCompIds) {
                $query->where(function ($q) use ($offreCompIds) {
                    $q->whereHas('competances', fn($subQuery) => $subQuery->whereIn('competances.id', $offreCompIds))
                        ->orWhereHas('cv', fn($subQuery) => $subQuery->whereNotNull('cv_text')->where('cv_text', '<>', ''));
                });
            })
            ->get();

        $scored = $particuliers->map(function ($particulier) use ($offre, $offreCompIds) {
            if (self::aCvLisible($particulier)) {
                $matching = MatchingService::calculer($particulier, $offre);

                return [
                    'particulier' => $particulier,
                    'score'       => $matching['score'],
                    'matched'     => $matching['criteres']['competences']['detail'] ?? [],
                    'source'      => 'cv',
                ];
            }

            $matched = $particulier->competances
                ->whereIn('id', $offreCompIds->toArray())
                ->pluck('nom')
                ->toArray();

            return [
                'particulier' => $particulier,
                'score'       => count($matched),
                'matched'     => $matched,
                'source'      => 'profil',
            ];
        });

        return $scored
            ->filter(fn($item) => $item['score'] > 0)
            ->sortByDesc('score')
            ->take($limit)
            ->values();
    }

    private static function aCvLisible(Particulier $particulier): bool
    {
        if (!$particulier->relationLoaded('cv')) {
            $particulier->load('cv');
        }

        return $particulier->cv
            ->contains(fn($cv) => is_string($cv->cv_text) && trim($cv->cv_text) !== '');
    }
}
