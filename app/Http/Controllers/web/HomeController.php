<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Offre;
use App\Models\Categorie;
use App\Models\Entreprise;
use App\Models\Particulier;
use App\Models\Candidature;
use App\Models\Utilisateur;
use App\Services\SuggestionService;
use App\Services\MatchingService;

class HomeController extends Controller
{
    public function index()
    {
        // ── Candidat ────────────────────────────────────────────────
        if (auth()->check() && auth()->user()->isParticulier()) {
            $particulier = auth()->user()->particulier->load(['cv', 'competances', 'candidatures']);

            $offres = Offre::active()
                ->with(['entreprise', 'categorie'])
                ->latest('date_publication')
                ->take(6)
                ->get();

            $dernieresCandidatures = $particulier->candidatures()
                ->with('offre.entreprise')
                ->latest()
                ->take(4)
                ->get();

            // Suggestions basées sur les compétences
            $suggestions = SuggestionService::offresParCompetences($particulier, 3);

            $stats = [
                'candidatures' => $particulier->candidatures()->count(),
                'acceptees'    => $particulier->candidatures()->where('statut', 'acceptee')->count(),
                'en_attente'   => $particulier->candidatures()->where('statut', 'en_attente')->count(),
                'competances'  => $particulier->competances()->count(),
            ];

            $offres = Offre::active()->with(['entreprise','categorie'])->latest()->take(6)->get();
            $offresMatchees = MatchingService::offresPourCandidат(
                $particulier,
                Offre::active()
                    ->with(['entreprise', 'categorie'])
                    ->whereNotIn('id', $particulier->candidatures->pluck('offre_id'))
                    ->get()
            )->take(3);

            $stats = [
                'candidatures' => $particulier->candidatures()->count(),
                'acceptees'    => $particulier->candidatures()->where('statut', 'acceptee')->count(),
                'en_attente'   => $particulier->candidatures()->where('statut', 'en_attente')->count(),
                'competances'  => $particulier->competances()->count(),
            ];

            return view('home', compact(
                'offres', 'particulier', 'dernieresCandidatures',
                'stats', 'suggestions', 'offresMatchees'
            ));
        }

        // ── Entreprise ───────────────────────────────────────────────
        if (auth()->check() && auth()->user()->isEntreprise()) {
            $entreprise = auth()->user()->entreprise;

            $offres = $entreprise->offres()
                ->with('categorie')
                ->withCount('candidatures')
                ->latest()
                ->take(5)
                ->get();

            $dernieresCandidatures = Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))
                ->with(['particulier.utilisateur', 'offre'])
                ->latest()
                ->take(5)
                ->get();

            $stats = [
                'total_offres'       => $entreprise->offres()->count(),
                'offres_actives'     => $entreprise->offres()->where('statut', 'active')->count(),
                'total_candidatures' => Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))->count(),
            ];

            return view('home', compact('entreprise', 'offres', 'dernieresCandidatures', 'stats'));
        }

        // ── Admin ────────────────────────────────────────────────────
        if (auth()->check() && auth()->user()->isAdmin()) {
            $stats = [
                'total_utilisateurs' => Utilisateur::count(),
                'total_entreprises'  => Utilisateur::where('role', 'entreprise')->count(),
                'total_offres'       => Offre::count(),
                'offres_actives'     => Offre::where('statut', 'active')->count(),
                'total_candidatures' => Candidature::count(),
            ];

            return view('home', compact('stats'));
        }

        // ── Visiteur ─────────────────────────────────────────────────
        $offres     = Offre::active()->with(['entreprise', 'categorie'])->latest('date_publication')->take(6)->get();
        $categories = Categorie::withCount(['offres' => fn($q) => $q->where('statut', 'active')])->get();

        $stats = [
            'offres'       => Offre::where('statut', 'active')->count(),
            'entreprises'  => Entreprise::count(),
            'particuliers' => Particulier::count(),
        ];

        return view('home', compact('offres', 'categories', 'stats'));
    }
}