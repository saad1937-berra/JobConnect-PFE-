<?php

namespace App\Services;

use App\Models\Offre;
use App\Models\Particulier;

class MatchingService
{
    // Pondération par niveau de maîtrise
    const NIVEAUX_POIDS = [
        'Expert'        => 1.0,
        'Avancé'        => 0.75,
        'Intermédiaire' => 0.5,
        'Débutant'      => 0.25,
    ];

    /**
     * Calculer le score de matching entre un particulier et une offre
     */
    public static function calculer(Particulier $particulier, Offre $offre): array
    {
        $criteres = [];
        $cvText = self::cvText($particulier);

        if ($cvText !== null) {
            $criteres['competences'] = self::scoreCompetencesDepuisCv($cvText, $offre);
            $criteres['mots_cles'] = self::scoreMotsClesDepuisCv($cvText, $offre);
            $criteres['localisation'] = self::scaleCritere(self::scoreLocalisation($particulier, $offre), 15);
            $criteres['niveau_etude'] = self::scaleCritere(self::scoreNiveauEtude($particulier, $offre), 10);
            $criteres['profil'] = self::scaleCritere(self::scoreProfil($particulier), 5);

            $total = array_sum(array_column($criteres, 'score'));

            return [
                'score'    => min(100, round($total)),
                'niveau'   => self::niveau($total),
                'couleur'  => self::couleur($total),
                'source'   => 'cv',
                'criteres' => $criteres,
            ];
        }

        // ── 1. Compétences pondérées (50 points) ─────────────────────
        $criteres['competences'] = self::scoreCompetences($particulier, $offre);

        // ── 2. Localisation (20 points) ──────────────────────────────
        $criteres['localisation'] = self::scoreLocalisation($particulier, $offre);

        // ── 3. Niveau d'études (20 points) ───────────────────────────
        $criteres['niveau_etude'] = self::scoreNiveauEtude($particulier, $offre);

        // ── 4. Profil complété (10 points) ───────────────────────────
        $criteres['profil'] = self::scoreProfil($particulier);

        $total = array_sum(array_column($criteres, 'score'));

        return [
            'score'    => min(100, round($total)),
            'niveau'   => self::niveau($total),
            'couleur'  => self::couleur($total),
            'source'   => 'profil',
            'criteres' => $criteres,
        ];
    }

    /**
     * Offres pour un candidat triées par score
     */
    public static function offresPourCandidат(Particulier $particulier, $offres): \Illuminate\Support\Collection
    {
        return $offres->map(function ($offre) use ($particulier) {
            if (!$offre->relationLoaded('competances')) {
                $offre->load('competances');
            }
            $offre->matching = self::calculer($particulier, $offre);
            return $offre;
        })
        ->filter(fn($o) => $o->matching['score'] > 0)
        ->sortByDesc(fn($o) => $o->matching['score'])
        ->values();
    }

    /**
     * Candidats pour une offre triés par score
     */
    public static function candidatsPourOffre(Offre $offre, $particuliers): \Illuminate\Support\Collection
    {
        return $particuliers->map(function ($particulier) use ($offre) {
            if (!$particulier->relationLoaded('competances')) {
                $particulier->load(['competances', 'cv']);
            }
            $particulier->matching = self::calculer($particulier, $offre);
            return $particulier;
        })
        ->filter(fn($p) => $p->matching['score'] > 0)
        ->sortByDesc(fn($p) => $p->matching['score'])
        ->values();
    }

    // ── Critères ─────────────────────────────────────────────────────

    private static function cvText(Particulier $particulier): ?string
    {
        if (!$particulier->relationLoaded('cv')) {
            $particulier->load('cv');
        }

        $text = $particulier->cv
            ->sortByDesc('created_at')
            ->first()?->cv_text;

        $text = is_string($text) ? trim($text) : '';

        return $text !== '' ? $text : null;
    }

    private static function scoreCompetencesDepuisCv(string $cvText, Offre $offre): array
    {
        $maxPoints = 60;

        if (!$offre->relationLoaded('competances')) {
            $offre->load('competances');
        }

        $competances = $offre->competances;

        if ($competances->isEmpty()) {
            return [
                'label'  => 'Competences CV',
                'score'  => 20,
                'max'    => $maxPoints,
                'detail' => [],
                'note'   => 'Aucune competence requise definie',
                'icone'  => 'fa-file-lines',
            ];
        }

        $cvText = self::normaliserTexte($cvText);
        $matched = [];

        foreach ($competances as $competance) {
            if (self::contientTerme($cvText, $competance->nom)) {
                $matched[] = $competance->nom;
            }
        }

        $score = min($maxPoints, round((count($matched) / max(1, $competances->count())) * $maxPoints));

        return [
            'label'          => 'Competences CV',
            'score'          => $score,
            'max'            => $maxPoints,
            'detail'         => $matched,
            'detail_niveau'  => collect($matched)->map(fn($nom) => ['nom' => $nom, 'niveau' => 'Detecte dans le CV', 'poids' => 1])->all(),
            'note'           => count($matched) . ' / ' . $competances->count() . ' competences detectees dans le CV',
            'icone'          => 'fa-file-lines',
        ];
    }

    private static function scoreMotsClesDepuisCv(string $cvText, Offre $offre): array
    {
        $maxPoints = 10;
        $cvText = self::normaliserTexte($cvText);
        $source = trim($offre->titre . ' ' . $offre->description . ' ' . ($offre->categorie->nom ?? ''));
        $motsCles = self::motsCles($source);
        $matched = [];

        foreach ($motsCles as $mot) {
            if (self::contientTerme($cvText, $mot)) {
                $matched[] = $mot;
            }
        }

        $score = empty($motsCles)
            ? 0
            : min($maxPoints, round((count($matched) / min(15, count($motsCles))) * $maxPoints));

        return [
            'label'  => 'Mots-cles CV',
            'score'  => $score,
            'max'    => $maxPoints,
            'detail' => array_slice($matched, 0, 10),
            'note'   => count($matched) . ' mot(s)-cle(s) de l\'offre detecte(s) dans le CV',
            'icone'  => 'fa-magnifying-glass',
        ];
    }

    private static function scaleCritere(array $critere, int $newMax): array
    {
        $oldMax = max(1, (int) ($critere['max'] ?? $newMax));
        $oldScore = (float) ($critere['score'] ?? 0);

        $critere['score'] = min($newMax, round(($oldScore / $oldMax) * $newMax));
        $critere['max'] = $newMax;

        return $critere;
    }

    private static function normaliserTexte(string $texte): string
    {
        $texte = mb_strtolower($texte);
        $texte = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texte) ?: $texte;
        $texte = preg_replace('/[^a-z0-9+#.\s-]/', ' ', $texte) ?? $texte;
        $texte = preg_replace('/\s+/', ' ', $texte) ?? $texte;

        return trim($texte);
    }

    private static function contientTerme(string $texteNormalise, string $terme): bool
    {
        $terme = self::normaliserTexte($terme);

        if ($terme === '') {
            return false;
        }

        return str_contains($texteNormalise, $terme);
    }

    private static function motsCles(string $texte): array
    {
        $stopWords = [
            'avec', 'dans', 'pour', 'nous', 'vous', 'des', 'les', 'une', 'aux', 'sur',
            'and', 'the', 'de', 'du', 'la', 'le', 'un', 'en', 'a', 'et', 'ou', 'au',
            'poste', 'profil', 'mission', 'missions', 'recherche', 'recherchons',
        ];

        $texte = self::normaliserTexte($texte);
        $words = preg_split('/\s+/', $texte) ?: [];
        $words = array_filter($words, fn($word) => mb_strlen($word) >= 3 && !in_array($word, $stopWords, true));

        return array_values(array_unique(array_slice($words, 0, 40)));
    }

    private static function scoreCompetences(Particulier $particulier, Offre $offre): array
    {
        $maxPoints = 50;

        $offreCompIds = $offre->competances->pluck('id');

        if ($offreCompIds->isEmpty()) {
            return [
                'label'   => 'Compétences',
                'score'   => 15,
                'max'     => $maxPoints,
                'detail'  => [],
                'note'    => 'Aucune compétence requise définie',
                'icone'   => 'fa-star',
            ];
        }

        $nbRequis    = $offreCompIds->count();
        $scoreTotal  = 0;
        $detail      = [];

        foreach ($particulier->competances as $comp) {
            if ($offreCompIds->contains($comp->id)) {
                $niveau = $comp->pivot->niveau ?? 'Débutant';
                $poids  = self::NIVEAUX_POIDS[$niveau] ?? 0.25;
                $scoreTotal += $poids;

                $detail[] = [
                    'nom'    => $comp->nom,
                    'niveau' => $niveau,
                    'poids'  => $poids,
                ];
            }
        }

        // Score = (somme des poids / nb compétences requises) * maxPoints
        $score = min($maxPoints, round(($scoreTotal / $nbRequis) * $maxPoints));

        // Résumé pour l'affichage
        $matched = array_column($detail, 'nom');

        return [
            'label'   => 'Compétences',
            'score'   => $score,
            'max'     => $maxPoints,
            'detail'  => $matched,
            'detail_niveau' => $detail,
            'note'    => count($matched) . ' / ' . $nbRequis . ' compétences (pondérées par niveau)',
            'icone'   => 'fa-star',
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
            return ['label' => 'Localisation', 'score' => $maxPoints, 'max' => $maxPoints, 'detail' => [], 'note' => 'Remote — compatible partout ✓', 'icone' => 'fa-map-marker-alt'];
        }

        if ($particulier->adresse) {
            $adresseCandidат = strtolower($particulier->adresse);
            $villeOffre      = trim(explode(',', $adresseOffre)[0]);
            $villeCandidат   = trim(explode(',', $adresseCandidат)[0]);

            if (str_contains($adresseCandidат, $villeOffre) || str_contains($adresseOffre, $villeCandidат)) {
                return ['label' => 'Localisation', 'score' => $maxPoints, 'max' => $maxPoints, 'detail' => [], 'note' => 'Même ville ✓', 'icone' => 'fa-map-marker-alt'];
            }

            return ['label' => 'Localisation', 'score' => 8, 'max' => $maxPoints, 'detail' => [], 'note' => 'Ville différente', 'icone' => 'fa-map-marker-alt'];
        }

        return ['label' => 'Localisation', 'score' => 5, 'max' => $maxPoints, 'detail' => [], 'note' => 'Adresse non renseignée', 'icone' => 'fa-map-marker-alt'];
    }

    private static function scoreNiveauEtude(Particulier $particulier, Offre $offre): array
    {
        $maxPoints = 20;

        $niveaux = [
            'Bac'      => 1,
            'Bac+2'    => 2,
            'Bac+3'    => 3,
            'Bac+4'    => 4,
            'Bac+5'    => 5,
            'Doctorat' => 6,
        ];

        if (!$offre->niveau_etude) {
            return ['label' => "Niveau d'études", 'score' => 10, 'max' => $maxPoints, 'detail' => [], 'note' => 'Non précisé par l\'offre', 'icone' => 'fa-graduation-cap'];
        }

        if (!$particulier->niveau_etude) {
            return ['label' => "Niveau d'études", 'score' => 5, 'max' => $maxPoints, 'detail' => [], 'note' => 'Non renseigné — complétez votre profil', 'icone' => 'fa-graduation-cap'];
        }

        $niveauCandidат = $niveaux[$particulier->niveau_etude] ?? 0;
        $niveauRequis   = $niveaux[$offre->niveau_etude]       ?? 0;

        if ($niveauCandidат >= $niveauRequis) {
            $score = $maxPoints;
            $note  = "{$particulier->niveau_etude} ✓ (requis : {$offre->niveau_etude})";
        } elseif ($niveauCandidат === $niveauRequis - 1) {
            $score = (int) round($maxPoints * 0.6);
            $note  = "{$particulier->niveau_etude} — légèrement insuffisant (requis : {$offre->niveau_etude})";
        } else {
            $score = (int) round($maxPoints * 0.2);
            $note  = "{$particulier->niveau_etude} — niveau insuffisant (requis : {$offre->niveau_etude})";
        }

        return ['label' => "Niveau d'études", 'score' => $score, 'max' => $maxPoints, 'detail' => [], 'note' => $note, 'icone' => 'fa-graduation-cap'];
    }

    private static function scoreProfil(Particulier $particulier): array
    {
        $maxPoints = 10;
        $score     = 0;

        if (!empty($particulier->bio))              $score += 2;
        if (!empty($particulier->tel))              $score += 1;
        if (!empty($particulier->adresse))          $score += 1;
        if (!empty($particulier->niveau_etude))     $score += 2;
        if ($particulier->cv->count() > 0)          $score += 3;
        if ($particulier->competances->count() > 0) $score += 1;

        return [
            'label'  => 'Profil complété',
            'score'  => $score,
            'max'    => $maxPoints,
            'detail' => [],
            'note'   => $score === $maxPoints ? 'Profil complet ✓' : "{$score}/{$maxPoints} points",
            'icone'  => 'fa-user-check',
        ];
    }

    // ── Helpers ──────────────────────────────────────────────────────

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
