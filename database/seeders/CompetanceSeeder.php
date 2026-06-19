<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Competance;

class CompetanceSeeder extends Seeder
{
    public function run(): void
    {
        $competances = [
            // Dev
            ['nom' => 'PHP',               'description' => 'Langage de programmation web côté serveur'],
            ['nom' => 'Laravel',           'description' => 'Framework PHP moderne'],
            ['nom' => 'JavaScript',        'description' => 'Langage de programmation web côté client'],
            ['nom' => 'React',             'description' => 'Bibliothèque JavaScript pour les interfaces'],
            ['nom' => 'Vue.js',            'description' => 'Framework JavaScript progressif'],
            ['nom' => 'Python',            'description' => 'Langage de programmation polyvalent'],
            ['nom' => 'MySQL',             'description' => 'Système de gestion de base de données'],
            ['nom' => 'Git',               'description' => 'Système de contrôle de version'],
            ['nom' => 'Docker',            'description' => 'Plateforme de conteneurisation'],
            ['nom' => 'Node.js',           'description' => 'Runtime JavaScript côté serveur'],

            // Business
            ['nom' => 'Excel',             'description' => 'Tableur Microsoft Office'],
            ['nom' => 'Comptabilité',      'description' => 'Gestion des comptes et bilans financiers'],
            ['nom' => 'Marketing Digital', 'description' => 'SEO, SEM, réseaux sociaux'],
            ['nom' => 'Gestion de projet', 'description' => 'Planification et suivi de projets'],
            ['nom' => 'Communication',     'description' => 'Rédaction, présentation, négociation'],

            // Design
            ['nom' => 'Figma',             'description' => 'Outil de design UI/UX'],
            ['nom' => 'Photoshop',         'description' => 'Logiciel de retouche photo'],
            ['nom' => 'Illustrator',       'description' => 'Logiciel de création vectorielle'],

            // Langues
            ['nom' => 'Anglais',           'description' => 'Maîtrise de la langue anglaise'],
            ['nom' => 'Français',          'description' => 'Maîtrise de la langue française'],
            ['nom' => 'Arabe',             'description' => 'Maîtrise de la langue arabe'],
        ];

        foreach ($competances as $comp) {
            Competance::updateOrCreate(['nom' => $comp['nom']], $comp);
        }
    }
}
