<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use App\Models\Offre;
use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EntrepriseController extends Controller
{
    // Publier une offre
    public function publier(Request $request)
    {
        $request->validate([
            'titre'          => 'required|string|max:255',
            'description'    => 'required|string',
            'categorie_id'   => 'required|exists:categories,id',
            'contrat'        => 'nullable|string',
            'duree'          => 'nullable|string',
            'localisation'   => 'nullable|string',
            'niveau_etude'   => 'nullable|string',
            'salaire'        => 'nullable|string',
            'date_expiration'=> 'nullable|date|after:today',
        ]);

        $entreprise = $request->user()->entreprise;

        $offre = Offre::create([
            ...$request->only(['titre', 'description', 'categorie_id', 'contrat',
                               'duree', 'localisation', 'niveau_etude', 'salaire', 'date_expiration']),
            'entreprise_id'    => $entreprise->id,
            'statut'           => 'active',
            'date_publication' => now(),
        ]);

        return response()->json(['message' => 'Offre publiée.', 'offre' => $offre], 201);
    }

    // Modifier une offre
    public function modifier(Request $request, $id)
    {
        $entreprise = $request->user()->entreprise;
        $offre = Offre::where('id', $id)->where('entreprise_id', $entreprise->id)->firstOrFail();

        $offre->update($request->only(['titre', 'description', 'categorie_id', 'contrat',
                                       'duree', 'localisation', 'niveau_etude', 'salaire',
                                       'date_expiration', 'statut']));

        return response()->json(['message' => 'Offre modifiée.', 'offre' => $offre]);
    }

    // Supprimer une offre
    public function supprimer(Request $request, $id)
    {
        $entreprise = $request->user()->entreprise;
        $offre = Offre::where('id', $id)->where('entreprise_id', $entreprise->id)->firstOrFail();
        $offre->delete();

        return response()->json(['message' => 'Offre supprimée.']);
    }

    // Consulter ses offres
    public function consulter(Request $request)
    {
        $entreprise = $request->user()->entreprise;

        $offres = $entreprise->offres()
            ->with('categorie')
            ->withCount('candidatures')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($offres);
    }

    // Télécharger le CV d'un candidat
    public function telechargerCV(Request $request, $candidatureId)
    {
        $entreprise = $request->user()->entreprise;

        $candidature = Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))
                                  ->with('particulier.cv')
                                  ->findOrFail($candidatureId);

        $cv = $candidature->particulier->cv->last();

        if (!$cv) {
            return response()->json(['message' => 'Aucun CV disponible.'], 404);
        }

        $extension = pathinfo($cv->cv_path, PATHINFO_EXTENSION) ?: 'pdf';
        $filename = 'CV_' . $cv->created_at->format('Y-m-d') . '.' . $extension;

        if (Storage::disk('local')->exists($cv->cv_path)) {
            return Storage::disk('local')->download($cv->cv_path, $filename);
        }

        if (Storage::disk('public')->exists($cv->cv_path)) {
            return Storage::disk('public')->download($cv->cv_path, $filename);
        }

        return response()->json(['message' => 'CV introuvable.'], 404);
    }

    // Changer le statut d'une candidature
    public function statutCandidature(Request $request, $candidatureId)
    {
        $request->validate([
            'statut'      => 'required|in:en_attente,acceptee,refusee,en_cours',
            'commentaire' => 'nullable|string',
        ]);

        $entreprise = $request->user()->entreprise;

        $candidature = Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))
                                  ->findOrFail($candidatureId);

        $candidature->update([
            'statut'      => $request->statut,
            'commentaire' => $request->commentaire,
        ]);

        return response()->json(['message' => 'Statut mis à jour.', 'candidature' => $candidature]);
    }

    // Dashboard entreprise
    public function consulterDashboard(Request $request)
    {
        $entreprise = $request->user()->entreprise;

        return response()->json([
            'total_offres'       => $entreprise->offres()->count(),
            'offres_actives'     => $entreprise->offres()->where('statut', 'active')->count(),
            'total_candidatures' => Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))->count(),
            'candidatures_recentes' => Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))
                                                   ->with(['particulier.utilisateur', 'offre'])
                                                   ->latest()
                                                   ->take(5)
                                                   ->get(),
        ]);
    }
}
