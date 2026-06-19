<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Competance;
use App\Models\Entreprise;
use App\Models\Offre;
use Illuminate\Database\Seeder;

class OffreSeeder extends Seeder
{
    public function run(): void
    {
        $entreprises = Entreprise::orderBy('id')->get();
        $categories = Categorie::orderBy('id')->get();
        $competances = Competance::orderBy('id')->get();

        if ($entreprises->isEmpty() || $categories->isEmpty()) {
            return;
        }

        $titresParCategorie = [
            'Informatique' => [
                'Developpeur Full-Stack Laravel Vue.js',
                'Developpeur Backend PHP Laravel',
                'Developpeur Mobile React Native',
                'Data Scientist IA Machine Learning',
                'Ingenieur DevOps Cloud Docker',
                'Administrateur Systemes et Reseaux',
                'Analyste Cybersecurite',
            ],
            'Marketing' => [
                'Chef de Projet Digital',
                'Community Manager',
                'Specialiste SEO SEA',
                'Content Manager',
                'Traffic Manager',
            ],
            'Finance' => [
                'Auditeur Financier Senior',
                'Controleur de Gestion',
                'Comptable Confirme',
                'Analyste Financier',
                'Charge de Tresorerie',
            ],
            'Ressources' => [
                'Charge de Recrutement',
                'Responsable RH',
                'Gestionnaire Paie',
                'Talent Acquisition Specialist',
            ],
            'Commerce' => [
                'Business Developer',
                'Account Manager',
                'Commercial Terrain',
                'Responsable Grands Comptes',
            ],
            'Ingenierie' => [
                'Ingenieur Genie Civil',
                'Conducteur de Travaux',
                'Ingenieur Methodes',
                'Dessinateur Projeteur AutoCAD',
            ],
            'Sante' => [
                'Infirmier Polyvalent',
                'Delegue Medical',
                'Pharmacien Assistant',
                'Coordinateur Medical',
            ],
            'Juridique' => [
                'Juriste Affaires',
                'Assistant Juridique',
                'Responsable Conformite',
                'Legal Counsel',
            ],
            'Design' => [
                'UX UI Designer',
                'Graphiste Digital',
                'Motion Designer',
                'Directeur Artistique Junior',
            ],
            'Enseignement' => [
                'Formateur Informatique',
                'Professeur Anglais',
                'Coach Soft Skills',
                'Concepteur Pedagogique',
            ],
        ];

        $contrats = ['CDI', 'CDD', 'Stage', 'Freelance', 'Alternance'];
        $villes = ['Casablanca', 'Rabat', 'Marrakech', 'Tanger', 'Fes', 'Agadir', 'Meknes', 'Oujda', 'Kenitra', 'Remote'];
        $niveaux = ['Bac', 'Bac+2', 'Bac+3', 'Bac+4', 'Bac+5', 'Doctorat'];
        $statuts = ['active', 'active', 'active', 'active', 'brouillon', 'expiree'];

        for ($i = 1; $i <= 1000; $i++) {
            $entreprise = $entreprises[($i - 1) % $entreprises->count()];
            $categorie = $categories[($i - 1) % $categories->count()];
            $key = $this->categorieKey($categorie->nom);
            $titres = $titresParCategorie[$key] ?? ['Consultant Polyvalent'];
            $titre = $titres[($i - 1) % count($titres)] . ' #' . str_pad((string) $i, 4, '0', STR_PAD_LEFT);
            $contrat = $contrats[$i % count($contrats)];
            $localisation = $villes[$i % count($villes)];
            $niveau = $niveaux[$i % count($niveaux)];
            $statut = $statuts[$i % count($statuts)];

            $offre = Offre::updateOrCreate(
                ['titre' => $titre],
                [
                    'entreprise_id'     => $entreprise->id,
                    'categorie_id'      => $categorie->id,
                    'description'       => $this->description($titre, $categorie->nom, $contrat),
                    'date_publication'  => now()->subDays($i % 60),
                    'date_expiration'   => now()->addDays(15 + ($i % 90)),
                    'contrat'           => $contrat,
                    'duree'             => in_array($contrat, ['Stage', 'CDD', 'Alternance'], true) ? (3 + ($i % 18)) . ' mois' : null,
                    'localisation'      => $localisation,
                    'niveau_etude'      => $niveau,
                    'statut'            => $statut,
                    'salaire'           => $this->salaire($contrat, $i),
                ]
            );

            if ($competances->isNotEmpty()) {
                $offset = $i % max(1, $competances->count());
                $ids = $competances
                    ->slice($offset)
                    ->merge($competances->slice(0, $offset))
                    ->take(3 + ($i % 4))
                    ->pluck('id')
                    ->all();

                $offre->competances()->sync($ids);
            }
        }
    }

    private function categorieKey(string $nom): string
    {
        return match (true) {
            str_contains($nom, 'Informatique') => 'Informatique',
            str_contains($nom, 'Marketing') => 'Marketing',
            str_contains($nom, 'Finance') => 'Finance',
            str_contains($nom, 'Ressources') => 'Ressources',
            str_contains($nom, 'Commerce') => 'Commerce',
            str_contains($nom, 'Ing') => 'Ingenierie',
            str_contains($nom, 'Sant') => 'Sante',
            str_contains($nom, 'Juridique') => 'Juridique',
            str_contains($nom, 'Design') => 'Design',
            str_contains($nom, 'Enseignement') => 'Enseignement',
            default => 'Autre',
        };
    }

    private function description(string $titre, string $categorie, string $contrat): string
    {
        return "{$titre}\n\n"
            . "Nous recherchons un profil motive pour rejoindre une equipe dynamique dans le domaine {$categorie}.\n\n"
            . "Missions :\n"
            . "- Participer aux projets de l'entreprise\n"
            . "- Collaborer avec les equipes internes\n"
            . "- Produire des livrables de qualite\n"
            . "- Proposer des ameliorations continues\n\n"
            . "Profil recherche :\n"
            . "- Formation adaptee au poste\n"
            . "- Bon relationnel et autonomie\n"
            . "- Maitrise des outils du metier\n"
            . "- Contrat propose : {$contrat}";
    }

    private function salaire(string $contrat, int $index): ?string
    {
        if ($contrat === 'Stage') {
            return (2000 + (($index % 6) * 500)) . ' MAD';
        }

        if ($contrat === 'Freelance') {
            return (500 + (($index % 10) * 100)) . ' MAD / jour';
        }

        $min = 4000 + (($index % 10) * 1000);
        $max = $min + 4000;

        return number_format($min, 0, ',', ' ') . ' - ' . number_format($max, 0, ',', ' ') . ' MAD';
    }
}
