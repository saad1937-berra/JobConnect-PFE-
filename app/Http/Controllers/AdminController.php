<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use App\Models\Entreprise;
use App\Models\Offre;
use App\Models\Candidature;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Valider une entreprise
    public function validerEntreprise(Request $request, $id)
    {
        $entreprise = Entreprise::findOrFail($id);
        $entreprise->utilisateur->update(['role' => 'entreprise']);

        return response()->json(['message' => 'Entreprise validée.']);
    }

    // Bloquer un utilisateur (entreprise ou particulier)
    public function bloquerUtilisateur(Request $request, $id)
    {
        $utilisateur = Utilisateur::findOrFail($id);
        $utilisateur->update(['role' => 'bloque']);
        $utilisateur->tokens()->delete();

        return response()->json(['message' => "Utilisateur {$utilisateur->nom} bloqué."]);
    }

    // Gérer les entreprises
    public function gererEntreprise(Request $request)
    {
        $entreprises = Entreprise::with('utilisateur')
            ->when($request->search, fn($q) => $q->where('nom', 'like', "%{$request->search}%"))
            ->paginate(20);

        return response()->json($entreprises);
    }

    // Consulter les statistiques globales
    public function consulterStatistiques()
    {
        return response()->json([
            'total_utilisateurs'  => Utilisateur::count(),
            'total_particuliers'  => Utilisateur::where('role', 'particulier')->count(),
            'total_entreprises'   => Utilisateur::where('role', 'entreprise')->count(),
            'total_offres'        => Offre::count(),
            'offres_actives'      => Offre::where('statut', 'active')->count(),
            'total_candidatures'  => Candidature::count(),
            'candidatures_par_statut' => Candidature::selectRaw('statut, count(*) as total')
                                                     ->groupBy('statut')
                                                     ->get(),
        ]);
    }
}
