<?php

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
                                        ->where('offre_id', $id)
                                        ->exists();
        }

        return view('offres.show', compact('offre', 'dejaCandidaté'));
    }
}
