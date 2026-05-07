<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Candidature;
use App\Models\Competance;
use App\Models\Cv;
use Illuminate\Http\Request;

class ParticulierWebController extends Controller
{
    public function profil()
    {
        $utilisateur = auth()->user();
        $particulier = $utilisateur->particulier->load(['cv', 'competances', 'candidatures']);
        $competances = Competance::all();

        return view('particulier.profil', compact('utilisateur', 'particulier', 'competances'));
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'bio'            => 'nullable|string',
            'tel'            => 'nullable|string|max:20',
            'adresse'        => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
        ]);

        auth()->user()->particulier->update(
            $request->only(['bio', 'tel', 'adresse', 'date_naissance'])
        );

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function uploadCV(Request $request)
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $particulier = auth()->user()->particulier;
        $path = $request->file('cv')->store("cvs/{$particulier->id}", 'public');

        Cv::create([
            'particulier_id' => $particulier->id,
            'cv_path'        => $path,
        ]);

        return back()->with('success', 'CV uploadé avec succès.');
    }

    public function ajouterCompetence(Request $request)
    {
        $request->validate([
            'competance_id' => 'required|exists:competances,id',
        ]);

        auth()->user()->particulier->competances()->syncWithoutDetaching([$request->competance_id]);

        return back()->with('success', 'Compétence ajoutée.');
    }

    public function supprimerCompetence($id)
    {
        auth()->user()->particulier->competances()->detach($id);

        return back()->with('success', 'Compétence supprimée.');
    }

    public function postuler(Request $request)
    {
        $request->validate([
            'offre_id' => 'required|exists:offres,id',
        ]);

        $particulier = auth()->user()->particulier;

        $existe = Candidature::where('particulier_id', $particulier->id)
                             ->where('offre_id', $request->offre_id)
                             ->exists();

        if ($existe) {
            return back()->with('error', 'Vous avez déjà postulé à cette offre.');
        }

        Candidature::create([
            'particulier_id' => $particulier->id,
            'offre_id'       => $request->offre_id,
            'statut'         => 'en_attente',
        ]);

        return back()->with('success', 'Candidature envoyée avec succès !');
    }

    public function candidatures()
    {
        $candidatures = auth()->user()->particulier
            ->candidatures()
            ->with('offre.entreprise')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('particulier.candidatures', compact('candidatures'));
    }
}
