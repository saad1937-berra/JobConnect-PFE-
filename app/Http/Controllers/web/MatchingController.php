<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Offre;
use App\Models\Particulier;
use App\Services\MatchingService;

class MatchingController extends Controller
{
    /**
     * Score de matching entre le candidat connecté et une offre
     */
    public function scoreOffre($offreId)
    {
        if (!auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('home')->with('error', 'Confirmez votre adresse email avant d acceder au matching.');
        }

        $particulier = auth()->user()->particulier->load(['competances', 'cv', 'candidatures']);
        $offre       = Offre::active()->with(['entreprise', 'categorie'])->findOrFail($offreId);
        $matching    = MatchingService::calculer($particulier, $offre);

        return view('matching.score-offre', compact('particulier', 'offre', 'matching'));
    }

    /**
     * Liste des offres avec score de matching pour le candidat
     */
    public function offresMatchees()
    {
        if (!auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('home')->with('error', 'Confirmez votre adresse email avant d acceder au matching.');
        }

        $particulier = auth()->user()->particulier->load(['competances', 'cv', 'candidatures']);

        $offres = Offre::active()
            ->with(['entreprise', 'categorie'])
            ->whereNotIn('id', $particulier->candidatures->pluck('offre_id'))
            ->get();

        $offresMatchees = MatchingService::offresPourCandidат($particulier, $offres);

        return view('matching.offres-matchees', compact('particulier', 'offresMatchees'));
    }

    /**
     * Score de matching entre tous les candidats et une offre (pour l'entreprise)
     */
    public function candidatsMatches($offreId)
    {
        $entreprise = auth()->user()->entreprise;
        if (!$entreprise->peutPublier()) {
            return redirect()
                ->route('entreprise.dashboard')
                ->with('error', 'Votre entreprise doit etre validee et son email confirme pour acceder au matching candidat.');
        }

        $offre      = Offre::where('entreprise_id', $entreprise->id)
                           ->with(['entreprise', 'categorie'])
                           ->findOrFail($offreId);

        $particuliers = Particulier::with(['utilisateur', 'competances', 'cv'])
            ->whereHas('utilisateur', fn($q) => $q
                ->where('role', 'particulier')
                ->whereNotNull('email_verified_at'))
            ->get();

        $candidatsMatches = MatchingService::candidatsPourOffre($offre, $particuliers);

        return view('matching.candidats-matches', compact('offre', 'candidatsMatches'));
    }
}
