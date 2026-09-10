<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use App\Models\Entreprise;
use App\Models\Offre;
use App\Models\Candidature;
use App\Models\Categorie;
use App\Models\Competance;
use App\Models\Report;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
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
            'offres_actives'          => Offre::active()->count(),
            'total_candidatures'      => Candidature::count(),
            'signalements_ouverts'    => Report::whereIn('status', ['nouveau', 'en_cours'])->count(),
            'messages_total'          => \App\Models\Message::count(),
            'utilisateurs_par_role'   => Utilisateur::selectRaw('role, count(*) as total')
                                            ->groupBy('role')
                                            ->get(),
            'candidatures_par_statut' => Candidature::selectRaw('statut, count(*) as total')
                                            ->groupBy('statut')
                                            ->get(),
            'offres_par_categorie'    => Categorie::withCount('offres')->orderByDesc('offres_count')->take(6)->get(),
            'entreprises_actives'     => Entreprise::withCount('offres')->orderByDesc('offres_count')->take(5)->get(),
        ];

        $entreprises = Entreprise::with('utilisateur')
            ->withCount('offres')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'entreprises'));
    }

    public function signalements(Request $request)
    {
        $reports = Report::with(['reporter', 'reported', 'conversation.lastMessage'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return view('admin.signalements', compact('reports'));
    }

    public function updateSignalement(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:nouveau,en_cours,traite,rejete',
            'admin_note' => 'required_if:status,traite,rejete|nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($request, $id) {
            $report = Report::lockForUpdate()->findOrFail($id);
            $report->fill([
                'status' => $request->status,
                'admin_note' => $request->admin_note,
            ]);
            if (!$report->isDirty(['status', 'admin_note'])) {
                return;
            }
            $report->resolved_at = in_array($request->status, ['traite', 'rejete'], true)
                ? ($report->resolved_at ?? now()) : null;
            $report->save();
            $conversation = Conversation::between($report->reporter, $request->user());
            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $request->user()->id,
                'body' => $report->followUpMessage(),
            ]);
            $conversation->update(['last_message_at' => now()]);
            NotificationService::envoyer($report->reporter_id, 'signalement', $report->followUpMessage());
        });

        return back()->with('success', 'Signalement mis a jour.');
    }

    // ── Entreprises ──────────────────────────────────────────────────

    public function entreprises(Request $request)
    {
        $entreprises = Entreprise::with('utilisateur')
            ->when($request->search, fn($q) => $q->where('nom', 'like', "%{$request->search}%"))
            ->when($request->statut_validation, fn($q) => $q->where('statut_validation', $request->statut_validation))
            ->withCount('offres')
            ->latest()
            ->paginate(20);

        return view('admin.entreprises', compact('entreprises'));
    }

    public function validerEntreprise($id)
    {
        $entreprise = Entreprise::with('utilisateur')->findOrFail($id);

        if (!$entreprise->utilisateur?->hasVerifiedEmail()) {
            return back()->with('error', 'Cette entreprise doit confirmer son adresse email avant validation.');
        }

        $entreprise->update([
            'statut_validation' => Entreprise::STATUT_VALIDEE,
        ]);

        return back()->with('success', 'Entreprise validee avec succes.');
    }

    public function refuserEntreprise($id)
    {
        Entreprise::findOrFail($id)->update([
            'statut_validation' => Entreprise::STATUT_REFUSEE,
        ]);

        return back()->with('success', 'Entreprise refusee.');
    }

    // ── Utilisateurs ─────────────────────────────────────────────────

    public function utilisateurs(Request $request)
    {
        $utilisateurs = Utilisateur::when($request->search, fn($q) =>
                            $q->where('email', 'like', "%{$request->search}%")
                              ->orWhere('nom',  'like', "%{$request->search}%"))
                          ->when($request->role, fn($q) => $q->where('role', $request->role))
                          ->latest()
                          ->paginate(20);

        return view('admin.utilisateurs', compact('utilisateurs'));
    }

    public function bloquerUtilisateur($id)
    {
        $utilisateur = Utilisateur::findOrFail($id);
        $utilisateur->update(['role' => 'bloque']);
        $utilisateur->tokens()->delete();

        return back()->with('success', "Utilisateur {$utilisateur->prenom} {$utilisateur->nom} bloqué.");
    }

    public function debloquerUtilisateur($id)
    {
        $utilisateur = Utilisateur::with(['particulier', 'entreprise'])->findOrFail($id);
        $role = $this->roleApresDeblocage($utilisateur);

        if (!$role) {
            return back()->with('error', 'Impossible de determiner le role a restaurer pour cet utilisateur.');
        }

        $utilisateur->update(['role' => $role]);

        return back()->with('success', "Utilisateur {$utilisateur->prenom} {$utilisateur->nom} debloque.");
    }

    private function roleApresDeblocage(Utilisateur $utilisateur): ?string
    {
        if ($utilisateur->entreprise) {
            return 'entreprise';
        }

        if ($utilisateur->particulier) {
            return 'particulier';
        }

        return null;
    }

    // ── Offres ───────────────────────────────────────────────────────

    public function offres(Request $request)
    {
        $offres = Offre::with(['entreprise', 'categorie'])
            ->when($request->search, fn($q) => $q->where('titre', 'like', "%{$request->search}%"))
            ->when($request->statut, fn($q) => $q->where('statut', $request->statut))
            ->latest()
            ->paginate(20);

        return view('admin.offres', compact('offres'));
    }

    public function supprimerOffre($id)
    {
        Offre::findOrFail($id)->delete();

        return back()->with('success', 'Offre supprimée.');
    }

    // ── Catégories ───────────────────────────────────────────────────

    public function categories()
    {
        $categories = Categorie::withCount('offres')->get();

        return view('admin.categories', compact('categories'));
    }

    public function storeCategorie(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:100|unique:categories,nom',
            'description' => 'nullable|string',
        ]);

        Categorie::create($request->only(['nom', 'description']));

        return back()->with('success', 'Catégorie créée.');
    }

    public function updateCategorie(Request $request, $id)
    {
        $request->validate([
            'nom'         => 'required|string|max:100|unique:categories,nom,' . $id,
            'description' => 'nullable|string',
        ]);

        Categorie::findOrFail($id)->update($request->only(['nom', 'description']));

        return back()->with('success', 'Catégorie modifiée.');
    }

    public function supprimerCategorie($id)
    {
        Categorie::findOrFail($id)->delete();

        return back()->with('success', 'Catégorie supprimée.');
    }

    // ── Compétences ──────────────────────────────────────────────────

    public function competances()
    {
        $competances = Competance::withCount('particuliers')->get();

        return view('admin.competances', compact('competances'));
    }

    public function storeCompetance(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:100|unique:competances,nom',
            'description' => 'nullable|string',
        ]);

        Competance::create($request->only(['nom', 'description']));

        return back()->with('success', 'Compétence créée.');
    }

    public function updateCompetance(Request $request, $id)
    {
        $request->validate([
            'nom'         => 'required|string|max:100|unique:competances,nom,' . $id,
            'description' => 'nullable|string',
        ]);

        Competance::findOrFail($id)->update($request->only(['nom', 'description']));

        return back()->with('success', 'Compétence modifiée.');
    }

    public function supprimerCompetance($id)
    {
        Competance::findOrFail($id)->delete();

        return back()->with('success', 'Compétence supprimée.');
    }
}
