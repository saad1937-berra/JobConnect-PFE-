<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Offre;
use App\Services\SuggestionService;

class SuggestionController extends Controller
{
    /**
     * Page suggestions pour le candidat
     */
    public function candidat()
    {
        $particulier  = auth()->user()->particulier->load(['competances', 'candidatures']);
        $suggestions  = SuggestionService::offresParCompetences($particulier, 12);

        return view('suggestions.candidat', compact('particulier', 'suggestions'));
    }

    /**
     * Page suggestions de candidats pour une offre donnée
     */
    public function entreprise($offreId)
    {
        $entreprise = auth()->user()->entreprise;
        $offre      = Offre::where('entreprise_id', $entreprise->id)->findOrFail($offreId);
        $suggestions = SuggestionService::candidatsParOffre($offre, 12);

        return view('suggestions.entreprise', compact('offre', 'suggestions'));
    }
}