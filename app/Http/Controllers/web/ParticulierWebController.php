<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Candidature;
use App\Models\Competance;
use App\Models\Offre;
use App\Models\Cv;
use App\Services\CvTextExtractor;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParticulierWebController extends Controller
{
    /**
     * Page d'accueil personnalisée pour le candidat après login
     */
    public function home()
    {
        $particulier  = auth()->user()->particulier;
        $nonLues      = auth()->user()->notifications()->whereNull('date_lecture')->count();

        // Offres recommandées (actives, les plus récentes)
        $offresRecentes = Offre::active()
            ->with(['entreprise', 'categorie'])
            ->latest('date_publication')
            ->take(6)
            ->get();

        // Mes dernières candidatures
        $dernieresCandidatures = $particulier->candidatures()
            ->with('offre.entreprise')
            ->latest()
            ->take(3)
            ->get();

        // Stats rapides
        $stats = [
            'candidatures'       => $particulier->candidatures()->count(),
            'acceptees'          => $particulier->candidatures()->where('statut', 'acceptee')->count(),
            'en_attente'         => $particulier->candidatures()->where('statut', 'en_attente')->count(),
            'competances'        => $particulier->competances()->count(),
        ];

        return view('particulier.home', compact(
            'particulier', 'offresRecentes',
            'dernieresCandidatures', 'stats', 'nonLues'
        ));
    }

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
            'niveau_etude'   => 'nullable|in:Bac,Bac+2,Bac+3,Bac+4,Bac+5,Doctorat',
        ]);

        auth()->user()->particulier->update(
            $request->only(['bio', 'tel', 'adresse', 'date_naissance', 'niveau_etude'])
        );

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function uploadCV(Request $request)
    {
        $request->validate([
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $particulier = auth()->user()->particulier;
        $path = $request->file('cv')->store("cvs/{$particulier->id}", 'local');

        Cv::create([
            'particulier_id' => $particulier->id,
            'cv_path'        => $path,
            'cv_text'        => CvTextExtractor::fromStoragePath($path, 'local'),
        ]);

        return back()->with('success', 'CV uploadé avec succès.');
    }

    public function telechargerCV($id)
    {
        $particulier = auth()->user()->particulier;
        $cv = Cv::where('particulier_id', $particulier->id)->findOrFail($id);

        return $this->downloadCvFile($cv);
    }

    public function genererCv()
    {
        $utilisateur = auth()->user();
        $particulier = $utilisateur->particulier->load([
            'competances',
            'candidatures.offre.entreprise',
        ]);

        $candidaturesAcceptees = $particulier->candidatures
            ->where('statut', 'acceptee')
            ->sortByDesc('created_at')
            ->take(4);

        return view('particulier.cv-template', compact(
            'utilisateur',
            'particulier',
            'candidaturesAcceptees'
        ));
    }

    public function ajouterCompetence(Request $request)
    {
        $request->validate([
            'competance_id' => 'required|exists:competances,id',
            'niveau'        => 'required|in:Débutant,Intermédiaire,Avancé,Expert',
        ]);

        // sync met à jour même si déjà existant
        auth()->user()->particulier->competances()->syncWithoutDetaching([
            $request->competance_id => ['niveau' => $request->niveau]
        ]);

        return back()->with('success', 'Compétence ajoutée.');
    }

    public function supprimerCompetence($id)
    {
        auth()->user()->particulier->competances()->detach($id);
        return back()->with('success', 'Compétence supprimée.');
    }

    public function postuler(Request $request)
    {
        $request->validate(['offre_id' => 'required|exists:offres,id']);

        $particulier = auth()->user()->particulier;
        $offre = Offre::active()->findOrFail($request->offre_id);

        $existe = Candidature::where('particulier_id', $particulier->id)
                             ->where('offre_id', $offre->id)
                             ->exists();

        if ($existe) {
            return back()->with('error', 'Vous avez déjà postulé à cette offre.');
        }

        $candidature = Candidature::create([
            'particulier_id' => $particulier->id,
            'offre_id'       => $offre->id,
            'statut'         => 'en_attente',
        ]);

        $candidature->load(['particulier.utilisateur', 'offre.entreprise.utilisateur']);
        NotificationService::nouvelleCandidature($candidature);

        return back()->with('success', 'Candidature envoyée avec succès !');
    }

    public function candidatures()
    {
        $candidatures = auth()->user()->particulier
            ->candidatures()
            ->whereHas('offre.entreprise.utilisateur', fn($q) => $q->where('role', 'entreprise'))
            ->with('offre.entreprise.utilisateur')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('particulier.candidatures', compact('candidatures'));
    }
    
     public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $particulier = auth()->user()->particulier;

        if ($particulier->photo) {
            Storage::disk('public')->delete($particulier->photo);
        }

        $path = $request->file('photo')->store("photos/particuliers/{$particulier->id}", 'public');
        $particulier->update(['photo' => $path]);

        return back()->with('success', 'Photo de profil mise à jour.');
    }
    private function downloadCvFile(Cv $cv)
    {
        $extension = pathinfo($cv->cv_path, PATHINFO_EXTENSION) ?: 'pdf';
        $filename = 'CV_' . $cv->created_at->format('Y-m-d') . '.' . $extension;

        if (Storage::disk('local')->exists($cv->cv_path)) {
            return Storage::disk('local')->download($cv->cv_path, $filename);
        }

        if (Storage::disk('public')->exists($cv->cv_path)) {
            return Storage::disk('public')->download($cv->cv_path, $filename);
        }

        abort(404, 'CV introuvable.');
    }
}
