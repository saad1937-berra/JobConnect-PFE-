<?php

namespace App\Http\Controllers;

use App\Models\Particulier;
use App\Models\Cv;
use App\Models\Competance;
use App\Models\Offre;
use App\Models\Candidature;
use App\Services\CvTextExtractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParticulierController extends Controller
{
    // Afficher le profil du particulier connecté
    public function gererProfil(Request $request)
    {
        $particulier = $request->user()->particulier;

        if (!$particulier) {
            return response()->json(['message' => 'Profil particulier introuvable.'], 404);
        }

        return response()->json($particulier->load(['utilisateur', 'competances', 'cv']));
    }

    // Mettre à jour le profil
    public function updateProfil(Request $request)
    {
        $request->validate([
            'bio'            => 'nullable|string',
            'tel'            => 'nullable|string|max:20',
            'adresse'        => 'nullable|string|max:255',
            'date_naissance' => 'nullable|date',
        ]);

        $particulier = $request->user()->particulier;
        $particulier->update($request->only(['bio', 'tel', 'adresse', 'date_naissance']));

        return response()->json(['message' => 'Profil mis à jour.', 'particulier' => $particulier]);
    }

    // Upload CV
    public function uploadCV(Request $request)
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $particulier = $request->user()->particulier;

        $path = $request->file('cv')->store("cvs/{$particulier->id}", 'local');

        $cv = Cv::create([
            'particulier_id' => $particulier->id,
            'cv_path'        => $path,
            'cv_text'        => CvTextExtractor::fromStoragePath($path, 'local'),
        ]);

        return response()->json(['message' => 'CV uploadé avec succès.', 'cv' => $cv], 201);
    }

    // Ajouter une compétence
    public function ajouterCompetence(Request $request)
    {
        $request->validate([
            'competance_id' => 'required|exists:competances,id',
            'niveau'        => 'required|in:Débutant,Intermédiaire,Avancé,Expert',
        ]);

        auth()->user()->particulier->competances()->syncWithoutDetaching([
            $request->competance_id => ['niveau' => $request->niveau]
        ]);

        return back()->with('success', 'Compétence ajoutée.');
    }

    // Supprimer une compétence
    public function supprimerCompetence(Request $request, $competanceId)
    {
        $particulier = $request->user()->particulier;
        $particulier->competances()->detach($competanceId);

        return response()->json(['message' => 'Compétence supprimée.']);
    }

    // Consulter les offres disponibles
    public function consulterOffres(Request $request)
    {
        $offres = Offre::active()
            ->with(['entreprise', 'categorie'])
            ->when($request->localisation, fn($q) => $q->where('localisation', 'like', "%{$request->localisation}%"))
            ->when($request->contrat,      fn($q) => $q->where('contrat', $request->contrat))
            ->when($request->categorie_id, fn($q) => $q->byCategorie($request->categorie_id))
            ->paginate(15);

        return response()->json($offres);
    }

    // Postuler à une offre
    public function postuler(Request $request)
    {
        $request->validate([
            'offre_id' => 'required|exists:offres,id',
        ]);

        $particulier = $request->user()->particulier;

        $existe = Candidature::where('particulier_id', $particulier->id)
                             ->where('offre_id', $request->offre_id)
                             ->exists();

        if ($existe) {
            return response()->json(['message' => 'Vous avez déjà postulé à cette offre.'], 409);
        }

        $candidature = Candidature::create([
            'particulier_id' => $particulier->id,
            'offre_id'       => $request->offre_id,
            'statut'         => 'en_attente',
        ]);

        return response()->json(['message' => 'Candidature envoyée.', 'candidature' => $candidature], 201);
    }

    // Suivre ses candidatures
    public function suivreCandidature(Request $request)
    {
        $particulier = $request->user()->particulier;

        $candidatures = $particulier->candidatures()
            ->with('offre.entreprise')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($candidatures);
    }
}
