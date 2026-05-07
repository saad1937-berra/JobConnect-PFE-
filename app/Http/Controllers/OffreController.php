<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Models\Categorie;
use Illuminate\Http\Request;

class OffreController extends Controller
{
    // Liste publique des offres
    public function index(Request $request)
    {
        $offres = Offre::active()
            ->with(['entreprise', 'categorie'])
            ->when($request->search,       fn($q) => $q->where('titre', 'like', "%{$request->search}%"))
            ->when($request->localisation, fn($q) => $q->where('localisation', 'like', "%{$request->localisation}%"))
            ->when($request->contrat,      fn($q) => $q->where('contrat', $request->contrat))
            ->when($request->categorie_id, fn($q) => $q->byCategorie($request->categorie_id))
            ->orderBy('date_publication', 'desc')
            ->paginate(15);

        return response()->json($offres);
    }

    // Détail d'une offre
    public function show($id)
    {
        $offre = Offre::with(['entreprise', 'categorie'])->findOrFail($id);
        return response()->json($offre);
    }
}
