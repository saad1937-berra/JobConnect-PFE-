<?php
// ══════════════════════════════════════════════════════
// app/Http/Controllers/Web/OffreWebController.php
// ══════════════════════════════════════════════════════
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Offre;
use App\Models\Categorie;
use App\Models\Candidature;
use Illuminate\Http\Request;

class OffreWebController extends Controller
{
    public function index(Request $request)
    {
        $offres = Offre::active()->with(['entreprise', 'categorie'])
            ->when($request->search,       fn($q) => $q->where('titre', 'like', "%{$request->search}%"))
            ->when($request->localisation, fn($q) => $q->where('localisation', 'like', "%{$request->localisation}%"))
            ->when($request->contrat,      fn($q) => $q->where('contrat', $request->contrat))
            ->when($request->categorie_id, fn($q) => $q->byCategorie($request->categorie_id))
            ->orderBy('date_publication', 'desc')
            ->paginate(15);

        $categories = Categorie::all();

        return view('offres.index', compact('offres', 'categories'));
    }

    public function show($id)
    {
        $offre = Offre::with(['entreprise', 'categorie'])->findOrFail($id);

        $dejaCandidaté = false;
        if (auth()->check() && auth()->user()->isParticulier()) {
            $dejaCandidaté = Candidature::where('particulier_id', auth()->user()->particulier->id)
                                        ->where('offre_id', $id)->exists();
        }

        return view('offres.show', compact('offre', 'dejaCandidaté'));
    }
}


// ══════════════════════════════════════════════════════
// app/Http/Controllers/Web/ParticulierWebController.php
// ══════════════════════════════════════════════════════
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

        auth()->user()->particulier->update($request->only(['bio','tel','adresse','date_naissance']));

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function uploadCV(Request $request)
    {
        $request->validate(['cv' => 'required|file|mimes:pdf,doc,docx|max:5120']);

        $particulier = auth()->user()->particulier;
        $path = $request->file('cv')->store("cvs/{$particulier->id}", 'public');
        Cv::create(['particulier_id' => $particulier->id, 'cv_path' => $path]);

        return back()->with('success', 'CV uploadé avec succès.');
    }

    public function ajouterCompetence(Request $request)
    {
        $request->validate(['competance_id' => 'required|exists:competances,id']);
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
        $request->validate(['offre_id' => 'required|exists:offres,id']);

        $particulier = auth()->user()->particulier;

        if (Candidature::where('particulier_id', $particulier->id)->where('offre_id', $request->offre_id)->exists()) {
            return back()->with('error', 'Vous avez déjà postulé à cette offre.');
        }

        Candidature::create(['particulier_id' => $particulier->id, 'offre_id' => $request->offre_id, 'statut' => 'en_attente']);

        return back()->with('success', 'Candidature envoyée avec succès !');
    }

    public function candidatures()
    {
        $candidatures = auth()->user()->particulier->candidatures()
            ->with('offre.entreprise')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('particulier.candidatures', compact('candidatures'));
    }
}


// ══════════════════════════════════════════════════════
// app/Http/Controllers/Web/EntrepriseWebController.php
// ══════════════════════════════════════════════════════
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Offre;
use App\Models\Categorie;
use App\Models\Candidature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EntrepriseWebController extends Controller
{
    public function dashboard()
    {
        $entreprise = auth()->user()->entreprise;

        $offres = $entreprise->offres()->with('categorie')->withCount('candidatures')->latest()->take(5)->get();

        $stats = [
            'total_offres'          => $entreprise->offres()->count(),
            'offres_actives'        => $entreprise->offres()->where('statut', 'active')->count(),
            'total_candidatures'    => Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))->count(),
            'candidatures_recentes' => Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))
                                        ->with(['particulier.utilisateur', 'offre'])->latest()->take(5)->get(),
        ];

        return view('entreprise.dashboard', compact('entreprise', 'offres', 'stats'));
    }

    public function profil()
    {
        return view('entreprise.profil', ['entreprise' => auth()->user()->entreprise]);
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:255',
            'secteur'     => 'nullable|string',
            'description' => 'nullable|string',
            'adresse'     => 'nullable|string',
            'site_web'    => 'nullable|url',
            'logo'        => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nom','secteur','description','adresse','site_web']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        auth()->user()->entreprise->update($data);
        return back()->with('success', 'Profil mis à jour.');
    }

    public function offres()
    {
        $offres = auth()->user()->entreprise->offres()->with('categorie')->withCount('candidatures')->latest()->paginate(15);
        return view('entreprise.offres', compact('offres'));
    }

    public function creerOffre()
    {
        $categories = Categorie::all();
        return view('entreprise.offre-form', compact('categories'));
    }

    public function storeOffre(Request $request)
    {
        $request->validate([
            'titre'        => 'required|string|max:255',
            'description'  => 'required|string',
            'categorie_id' => 'required|exists:categories,id',
        ]);

        auth()->user()->entreprise->offres()->create([
            ...$request->only(['titre','description','categorie_id','contrat','duree','localisation','niveau_etude','salaire','date_expiration','statut']),
            'date_publication' => now(),
            'statut'           => $request->statut ?? 'active',
        ]);

        return redirect()->route('entreprise.offres')->with('success', 'Offre publiée avec succès !');
    }

    public function editOffre($id)
    {
        $offre      = Offre::where('entreprise_id', auth()->user()->entreprise->id)->findOrFail($id);
        $categories = Categorie::all();
        return view('entreprise.offre-form', compact('offre', 'categories'));
    }

    public function updateOffre(Request $request, $id)
    {
        $offre = Offre::where('entreprise_id', auth()->user()->entreprise->id)->findOrFail($id);
        $offre->update($request->only(['titre','description','categorie_id','contrat','duree','localisation','niveau_etude','salaire','date_expiration','statut']));
        return redirect()->route('entreprise.offres')->with('success', 'Offre modifiée.');
    }

    public function supprimerOffre($id)
    {
        Offre::where('entreprise_id', auth()->user()->entreprise->id)->findOrFail($id)->delete();
        return back()->with('success', 'Offre supprimée.');
    }

    public function candidatures()
    {
        $entreprise   = auth()->user()->entreprise;
        $candidatures = Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))
                                   ->with(['particulier.utilisateur', 'offre'])
                                   ->latest()->paginate(20);
        return view('entreprise.candidatures', compact('candidatures'));
    }

    public function showCandidature($id)
    {
        $entreprise  = auth()->user()->entreprise;
        $candidature = Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))
                                  ->with(['particulier.utilisateur', 'particulier.cv', 'particulier.competances', 'offre'])
                                  ->findOrFail($id);
        return view('entreprise.candidature-show', compact('candidature'));
    }

    public function changerStatut(Request $request, $id)
    {
        $request->validate(['statut' => 'required|in:en_attente,acceptee,refusee,en_cours', 'commentaire' => 'nullable|string']);

        $entreprise  = auth()->user()->entreprise;
        $candidature = Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))->findOrFail($id);
        $candidature->update(['statut' => $request->statut, 'commentaire' => $request->commentaire]);

        return back()->with('success', 'Statut mis à jour.');
    }

    public function telechargerCV($id)
    {
        $entreprise  = auth()->user()->entreprise;
        $candidature = Candidature::whereHas('offre', fn($q) => $q->where('entreprise_id', $entreprise->id))
                                  ->with('particulier.cv')->findOrFail($id);
        $cv = $candidature->particulier->cv->last();
        if (!$cv) return back()->with('error', 'Aucun CV disponible.');
        return Storage::disk('public')->download($cv->cv_path);
    }
}


// ══════════════════════════════════════════════════════
// app/Http/Controllers/Web/AdminWebController.php
// ══════════════════════════════════════════════════════
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use App\Models\Entreprise;
use App\Models\Offre;
use App\Models\Candidature;
use App\Models\Categorie;
use App\Models\Competance;
use Illuminate\Http\Request;

class AdminWebController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_utilisateurs'      => Utilisateur::count(),
            'total_particuliers'      => Utilisateur::where('role', 'particulier')->count(),
            'total_entreprises'       => Utilisateur::where('role', 'entreprise')->count(),
            'total_offres'            => Offre::count(),
            'offres_actives'          => Offre::where('statut', 'active')->count(),
            'total_candidatures'      => Candidature::count(),
            'candidatures_par_statut' => Candidature::selectRaw('statut, count(*) as total')->groupBy('statut')->get(),
        ];

        $entreprises = Entreprise::with('utilisateur')->withCount('offres')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'entreprises'));
    }

    public function entreprises(Request $request)
    {
        $entreprises = Entreprise::with('utilisateur')
            ->when($request->search, fn($q) => $q->where('nom', 'like', "%{$request->search}%"))
            ->withCount('offres')->latest()->paginate(20);
        return view('admin.entreprises', compact('entreprises'));
    }

    public function validerEntreprise($id)
    {
        Entreprise::findOrFail($id)->utilisateur->update(['role' => 'entreprise']);
        return back()->with('success', 'Entreprise validée.');
    }

    public function utilisateurs(Request $request)
    {
        $utilisateurs = Utilisateur::when($request->search, fn($q) => $q->where('email', 'like', "%{$request->search}%"))
                                   ->when($request->role, fn($q) => $q->where('role', $request->role))
                                   ->latest()->paginate(20);
        return view('admin.utilisateurs', compact('utilisateurs'));
    }

    public function bloquerUtilisateur($id)
    {
        Utilisateur::findOrFail($id)->update(['role' => 'bloque']);
        return back()->with('success', 'Utilisateur bloqué.');
    }

    public function offres(Request $request)
    {
        $offres = Offre::with(['entreprise', 'categorie'])
            ->when($request->search, fn($q) => $q->where('titre', 'like', "%{$request->search}%"))
            ->latest()->paginate(20);
        return view('admin.offres', compact('offres'));
    }

    public function supprimerOffre($id)
    {
        Offre::findOrFail($id)->delete();
        return back()->with('success', 'Offre supprimée.');
    }

    public function categories()
    {
        $categories = Categorie::withCount('offres')->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategorie(Request $request)
    {
        $request->validate(['nom' => 'required|string|max:100', 'description' => 'nullable|string']);
        Categorie::create($request->only(['nom', 'description']));
        return back()->with('success', 'Catégorie créée.');
    }

    public function updateCategorie(Request $request, $id)
    {
        Categorie::findOrFail($id)->update($request->only(['nom', 'description']));
        return back()->with('success', 'Catégorie modifiée.');
    }

    public function supprimerCategorie($id)
    {
        Categorie::findOrFail($id)->delete();
        return back()->with('success', 'Catégorie supprimée.');
    }

    public function competances()
    {
        $competances = Competance::withCount('particuliers')->get();
        return view('admin.competances', compact('competances'));
    }

    public function storeCompetance(Request $request)
    {
        $request->validate(['nom' => 'required|string|max:100', 'description' => 'nullable|string']);
        Competance::create($request->only(['nom', 'description']));
        return back()->with('success', 'Compétence créée.');
    }

    public function updateCompetance(Request $request, $id)
    {
        Competance::findOrFail($id)->update($request->only(['nom', 'description']));
        return back()->with('success', 'Compétence modifiée.');
    }

    public function supprimerCompetance($id)
    {
        Competance::findOrFail($id)->delete();
        return back()->with('success', 'Compétence supprimée.');
    }
}


// ══════════════════════════════════════════════════════
// app/Http/Controllers/Web/NotificationWebController.php
// ══════════════════════════════════════════════════════
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationWebController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function marquerLu($id)
    {
        Notification::where('utilisateur_id', auth()->id())->findOrFail($id)->marquerLu();
        return back()->with('success', 'Notification lue.');
    }

    public function marquerToutLu()
    {
        auth()->user()->notifications()->whereNull('date_lecture')->update(['date_lecture' => now()]);
        return back()->with('success', 'Toutes les notifications marquées comme lues.');
    }
}
