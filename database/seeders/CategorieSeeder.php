<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorie;

class CategorieSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nom' => 'Informatique & Tech',      'description' => 'Développement, cybersécurité, réseaux, IA...'],
            ['nom' => 'Marketing & Communication', 'description' => 'Digital, brand, contenu, SEO...'],
            ['nom' => 'Finance & Comptabilité',    'description' => 'Audit, contrôle de gestion, banque...'],
            ['nom' => 'Ressources Humaines',       'description' => 'Recrutement, formation, paie...'],
            ['nom' => 'Commerce & Vente',          'description' => 'Business development, account manager...'],
            ['nom' => 'Ingénierie & BTP',          'description' => 'Génie civil, mécanique, électrique...'],
            ['nom' => 'Santé & Médical',           'description' => 'Médecins, infirmiers, pharmaciens...'],
            ['nom' => 'Juridique',                 'description' => 'Avocats, juristes, notaires...'],
            ['nom' => 'Design & Créatif',          'description' => 'UI/UX, graphisme, audiovisuel...'],
            ['nom' => 'Enseignement & Formation',  'description' => 'Professeurs, formateurs, coaches...'],
        ];

        foreach ($categories as $cat) {
            Categorie::create($cat);
        }
    }
}