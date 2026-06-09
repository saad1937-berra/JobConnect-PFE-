<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Offre;
use App\Models\Categorie;
use App\Models\Candidature;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Competance;

class EntrepriseWebController extends Controller
{
    public function dashboard()
    {
        $entreprise = auth()->user()->entreprise;

        $offres = $entreprise->offres()
            ->with('categorie')
            ->withCount('candidatures')
            ->latest()
            ->take(5)
            ->get();

        $stats = [
            'total_offres'          => $entreprise->offres()->count(),
            'offres_actives'        => $entreprise->offres()->where('statut', 'active')->count(),
            'total_candidatures'    => Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))->count(),
            'candidatures_recentes' => Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))
                                            ->with(['particulier.utilisateur', 'offre'])
                                            ->latest()
                                            ->take(5)
                                            ->get(),
        ];

        return view('entreprise.dashboard', compact('entreprise', 'offres', 'stats'));
    }

    public function profil()
    {
        $entreprise = auth()->user()->entreprise;
        return view('entreprise.profil', compact('entreprise'));
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:255',
            'secteur'     => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'adresse'     => 'nullable|string|max:255',
            'site_web'    => 'nullable|url|max:255',
            'logo'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->only(['nom', 'secteur', 'description', 'adresse', 'site_web']);

        if ($request->hasFile('logo')) {
            $ancien = auth()->user()->entreprise->logo;
            if ($ancien) Storage::disk('public')->delete($ancien);
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        auth()->user()->entreprise->update($data);

        return back()->with('success', 'Profil entreprise mis à jour.');
    }

    public function offres()
    {
        $offres = auth()->user()->entreprise
            ->offres()
            ->with('categorie')
            ->withCount('candidatures')
            ->latest()
            ->paginate(15);

        return view('entreprise.offres', compact('offres'));
    }

    public function creerOffre()
    {
        $categories = Categorie::all();
        $competances = Competance::all();
        return view('entreprise.offre-form', compact('categories', 'competances'));
    }

    public function storeOffre(Request $request)
    {
        $request->validate([
            'titre'           => 'required|string|max:255',
            'description'     => 'required|string',
            'categorie_id'    => 'required|exists:categories,id',
            'contrat'         => 'nullable|string|max:50',
            'duree'           => 'nullable|string|max:100',
            'localisation'    => 'nullable|string|max:255',
            'niveau_etude'    => 'nullable|string|max:50',
            'salaire'         => 'nullable|string|max:100',
            'date_expiration' => 'nullable|date|after:today',
            'statut'          => 'nullable|in:active,brouillon,expiree',
        ]);

        $offre = auth()->user()->entreprise->offres()->create([
            ...$request->only([
                'titre', 'description', 'categorie_id', 'contrat',
                'duree', 'localisation', 'niveau_etude', 'salaire', 'date_expiration',
            ]),
            'statut'           => $request->statut ?? 'active',
            'date_publication' => now(),
        ]);

        if ($request->has('competances')) {
            $offre->competances()->sync($request->competances);
        }

        return redirect()->route('entreprise.offres')->with('success', 'Offre publiée avec succès !');
    }

    public function editOffre($id)
    {
        $offre      = Offre::where('entreprise_id', auth()->user()->entreprise->id)->findOrFail($id);
        $categories = Categorie::all();
        $competances = Competance::all();
        return view('entreprise.offre-form', compact('offre', 'categories', 'competances'));
    }

    public function updateOffre(Request $request, $id)
    {
        $request->validate([
            'titre'           => 'required|string|max:255',
            'description'     => 'required|string',
            'categorie_id'    => 'required|exists:categories,id',
            'contrat'         => 'nullable|string|max:50',
            'duree'           => 'nullable|string|max:100',
            'localisation'    => 'nullable|string|max:255',
            'niveau_etude'    => 'nullable|string|max:50',
            'salaire'         => 'nullable|string|max:100',
            'date_expiration' => 'nullable|date',
            'statut'          => 'nullable|in:active,brouillon,expiree',
        ]);

        $offre = Offre::where('entreprise_id', auth()->user()->entreprise->id)->findOrFail($id);
        $offre->update($request->only([
            'titre', 'description', 'categorie_id', 'contrat',
            'duree', 'localisation', 'niveau_etude', 'salaire', 'date_expiration', 'statut',
        ]));

        $offre->competances()->sync($request->competances ?? []);

        return redirect()->route('entreprise.offres')->with('success', 'Offre modifiée avec succès.');
    }

    public function supprimerOffre($id)
    {
        Offre::where('entreprise_id', auth()->user()->entreprise->id)->findOrFail($id)->delete();
        return back()->with('success', 'Offre supprimée.');
    }

    public function candidatures()
    {
        $entreprise = auth()->user()->entreprise;

        $candidatures = Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))
            ->with(['particulier.utilisateur', 'offre'])
            ->latest()
            ->paginate(20);

        return view('entreprise.candidatures', compact('candidatures'));
    }

    public function showCandidature($id)
    {
        $entreprise = auth()->user()->entreprise;

        $candidature = Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))
            ->with([
                'particulier.utilisateur',
                'particulier.cv',
                'particulier.competances',
                'offre',
            ])
            ->findOrFail($id);

        return view('entreprise.candidature-show', compact('candidature'));
    }

    public function changerStatut(Request $request, $id)
    {
        $request->validate([
            'statut'      => 'required|in:en_attente,acceptee,refusee,en_cours',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $entreprise  = auth()->user()->entreprise;
        $candidature = Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))
                                  ->findOrFail($id);

        $ancienStatut = $candidature->statut;

        $candidature->update([
            'statut'      => $request->statut,
            'commentaire' => $request->commentaire,
        ]);

        // ✅ Notifier le candidat seulement si le statut a changé
        if ($ancienStatut !== $request->statut) {
            $candidature->load(['particulier.utilisateur', 'offre']);
            NotificationService::statutCandidature($candidature);
        }

        return back()->with('success', 'Statut de la candidature mis à jour.');
    }

    public function telechargerCV($id)
    {
        $entreprise  = auth()->user()->entreprise;
        $candidature = Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))
            ->with('particulier.cv')
            ->findOrFail($id);

        $cv = $candidature->particulier->cv->last();

        if (!$cv) {
            return back()->with('error', 'Aucun CV disponible pour ce candidat.');
        }

        return Storage::disk('public')->download($cv->cv_path);
    }
}