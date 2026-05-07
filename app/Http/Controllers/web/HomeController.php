<?php
// app/Http/Controllers/Web/HomeController.php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Offre;
use App\Models\Categorie;
use App\Models\Utilisateur;
use App\Models\Entreprise;
use App\Models\Particulier;

class HomeController extends Controller
{
    public function index()
    {
        $offres     = Offre::active()->with(['entreprise', 'categorie'])->latest('date_publication')->take(6)->get();
        $categories = Categorie::withCount(['offres' => fn($q) => $q->where('statut','active')])->get();

        $stats = [
            'offres'      => Offre::where('statut','active')->count(),
            'entreprises' => Entreprise::count(),
            'particuliers'=> Particulier::count(),
        ];

        return view('home', compact('offres', 'categories', 'stats'));
    }
}
