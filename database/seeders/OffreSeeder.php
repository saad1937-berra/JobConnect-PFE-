<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Entreprise;
use App\Models\Categorie;
use App\Models\Offre;

class OffreSeeder extends Seeder
{
    public function run(): void
    {
        $tech    = Categorie::where('nom', 'like', '%Informatique%')->first();
        $finance = Categorie::where('nom', 'like', '%Finance%')->first();
        $market  = Categorie::where('nom', 'like', '%Marketing%')->first();
        $btp     = Categorie::where('nom', 'like', '%Ingénierie%')->first();
        $design  = Categorie::where('nom', 'like', '%Design%')->first();

        $techmaroc  = Entreprise::where('nom', 'like', '%TechMaroc%')->first();
        $atlas      = Entreprise::where('nom', 'like', '%Atlas%')->first();
        $sahra      = Entreprise::where('nom', 'like', '%Sahra%')->first();
        $build      = Entreprise::where('nom', 'like', '%Build%')->first();
        $innovate   = Entreprise::where('nom', 'like', '%Innovate%')->first();

        $offres = [
            [
                'entreprise_id'   => $techmaroc->id,
                'categorie_id'    => $tech->id,
                'titre'           => 'Développeur Full-Stack Laravel / Vue.js',
                'description'     => "Nous recherchons un développeur Full-Stack passionné pour rejoindre notre équipe.\n\nMissions :\n- Développer et maintenir des applications web avec Laravel et Vue.js\n- Participer aux revues de code et aux rituels agile\n- Collaborer avec l'équipe design pour intégrer les maquettes\n- Optimiser les performances des applications existantes\n\nProfil recherché :\n- Maîtrise de Laravel 10+ et Vue.js 3\n- Bonne connaissance de MySQL et Redis\n- Expérience avec Git et les méthodologies agile\n- Autonomie et esprit d'équipe",
                'contrat'         => 'CDI',
                'localisation'    => 'Casablanca',
                'niveau_etude'    => 'Bac+3',
                'salaire'         => '8 000 – 12 000 MAD',
                'duree'           => null,
                'statut'          => 'active',
                'date_expiration' => now()->addMonths(2),
            ],
            [
                'entreprise_id'   => $techmaroc->id,
                'categorie_id'    => $tech->id,
                'titre'           => 'Développeur Mobile React Native',
                'description'     => "TechMaroc Solutions recrute un développeur mobile pour renforcer son équipe.\n\nMissions :\n- Développer des applications mobiles iOS et Android avec React Native\n- Intégrer des APIs REST et GraphQL\n- Assurer la qualité du code via des tests unitaires\n\nProfil :\n- 2 ans d'expérience minimum en React Native\n- Connaissance de TypeScript\n- Expérience avec les stores App Store et Google Play",
                'contrat'         => 'CDI',
                'localisation'    => 'Casablanca',
                'niveau_etude'    => 'Bac+3',
                'salaire'         => '9 000 – 14 000 MAD',
                'duree'           => null,
                'statut'          => 'active',
                'date_expiration' => now()->addMonths(3),
            ],
            [
                'entreprise_id'   => $innovate->id,
                'categorie_id'    => $tech->id,
                'titre'           => 'Data Scientist – IA & Machine Learning',
                'description'     => "Innovate Maroc cherche un Data Scientist pour développer nos modèles d'IA.\n\nMissions :\n- Concevoir et entraîner des modèles de machine learning\n- Analyser de grands volumes de données\n- Collaborer avec les équipes produit\n\nProfil :\n- Maîtrise de Python (Pandas, Scikit-learn, TensorFlow)\n- Expérience en NLP et computer vision\n- Bac+5 en data science, statistiques ou informatique",
                'contrat'         => 'CDI',
                'localisation'    => 'Casablanca',
                'niveau_etude'    => 'Bac+5',
                'salaire'         => '12 000 – 18 000 MAD',
                'duree'           => null,
                'statut'          => 'active',
                'date_expiration' => now()->addMonths(2),
            ],
            [
                'entreprise_id'   => $innovate->id,
                'categorie_id'    => $tech->id,
                'titre'           => 'Stage – Développeur Backend Node.js',
                'description'     => "Stage de fin d'études chez Innovate Maroc.\n\nMissions :\n- Développer des APIs RESTful avec Node.js et Express\n- Travailler sur des projets réels avec l'équipe tech\n- Participer aux réunions agile\n\nProfil :\n- Étudiant Bac+4/5 en informatique\n- Connaissance de Node.js, Express, MongoDB\n- Curiosité et envie d'apprendre",
                'contrat'         => 'Stage',
                'localisation'    => 'Casablanca',
                'niveau_etude'    => 'Bac+4',
                'salaire'         => '2 500 MAD',
                'duree'           => '6 mois',
                'statut'          => 'active',
                'date_expiration' => now()->addMonths(1),
            ],
            [
                'entreprise_id'   => $atlas->id,
                'categorie_id'    => $finance->id,
                'titre'           => 'Auditeur Financier Senior',
                'description'     => "Atlas Group International recrute un auditeur financier expérimenté.\n\nMissions :\n- Réaliser des missions d'audit légal et contractuel\n- Analyser les états financiers et les contrôles internes\n- Rédiger les rapports d'audit\n- Encadrer les auditeurs juniors\n\nProfil :\n- 5 ans d'expérience en audit\n- Maîtrise des normes IFRS et ISA\n- DESA ou Master en finance/comptabilité",
                'contrat'         => 'CDI',
                'localisation'    => 'Casablanca',
                'niveau_etude'    => 'Bac+5',
                'salaire'         => '15 000 – 20 000 MAD',
                'duree'           => null,
                'statut'          => 'active',
                'date_expiration' => now()->addMonths(2),
            ],
            [
                'entreprise_id'   => $atlas->id,
                'categorie_id'    => $finance->id,
                'titre'           => 'Contrôleur de Gestion',
                'description'     => "Poste de contrôleur de gestion au sein du groupe Atlas.\n\nMissions :\n- Élaborer et suivre les budgets\n- Produire les tableaux de bord mensuels\n- Analyser les écarts et proposer des actions correctives\n\nProfil :\n- Formation Bac+5 en finance ou gestion\n- Maîtrise avancée d'Excel et Power BI\n- 3 ans d'expérience minimum",
                'contrat'         => 'CDI',
                'localisation'    => 'Casablanca',
                'niveau_etude'    => 'Bac+5',
                'salaire'         => '10 000 – 14 000 MAD',
                'duree'           => null,
                'statut'          => 'active',
                'date_expiration' => now()->addMonths(3),
            ],
            [
                'entreprise_id'   => $sahra->id,
                'categorie_id'    => $market->id,
                'titre'           => 'Chef de Projet Digital',
                'description'     => "Sahra Digital Agency recherche un chef de projet pour gérer ses clients.\n\nMissions :\n- Gérer les projets digitaux de A à Z\n- Coordonner les équipes créatives et techniques\n- Assurer la relation client\n- Suivre les KPIs et reporter les résultats\n\nProfil :\n- Expérience en gestion de projet digital\n- Connaissance des outils : Trello, Notion, Google Analytics\n- Excellentes compétences en communication",
                'contrat'         => 'CDI',
                'localisation'    => 'Rabat',
                'niveau_etude'    => 'Bac+3',
                'salaire'         => '7 000 – 10 000 MAD',
                'duree'           => null,
                'statut'          => 'active',
                'date_expiration' => now()->addMonths(2),
            ],
            [
                'entreprise_id'   => $sahra->id,
                'categorie_id'    => $market->id,
                'titre'           => 'Community Manager',
                'description'     => "Poste de community manager pour gérer les réseaux sociaux de nos clients.\n\nMissions :\n- Créer et planifier du contenu engageant\n- Animer les communautés sur Instagram, Facebook, LinkedIn\n- Réaliser des reportings mensuels\n\nProfil :\n- Créatif, réactif et passionné par les réseaux sociaux\n- Maîtrise des outils de création (Canva, Adobe)\n- Bonne plume en français et arabe",
                'contrat'         => 'CDD',
                'localisation'    => 'Remote',
                'niveau_etude'    => 'Bac+2',
                'salaire'         => '4 000 – 6 000 MAD',
                'duree'           => '12 mois',
                'statut'          => 'active',
                'date_expiration' => now()->addMonths(1),
            ],
            [
                'entreprise_id'   => $build->id,
                'categorie_id'    => $btp->id,
                'titre'           => 'Ingénieur Génie Civil',
                'description'     => "BuildMaroc Construction recrute un ingénieur génie civil pour ses chantiers.\n\nMissions :\n- Superviser les travaux de construction\n- Vérifier la conformité des ouvrages\n- Coordonner les sous-traitants\n- Gérer les plannings et budgets de chantier\n\nProfil :\n- Diplôme d'ingénieur en génie civil\n- 3 ans d'expérience sur chantier\n- Maîtrise d'AutoCAD et MS Project",
                'contrat'         => 'CDI',
                'localisation'    => 'Marrakech',
                'niveau_etude'    => 'Bac+5',
                'salaire'         => '9 000 – 13 000 MAD',
                'duree'           => null,
                'statut'          => 'active',
                'date_expiration' => now()->addMonths(2),
            ],
            [
                'entreprise_id'   => $techmaroc->id,
                'categorie_id'    => $design->id,
                'titre'           => 'UX/UI Designer',
                'description'     => "Nous cherchons un designer UX/UI créatif pour améliorer l'expérience de nos produits.\n\nMissions :\n- Concevoir des interfaces intuitives et esthétiques\n- Réaliser des wireframes, prototypes et maquettes\n- Mener des tests utilisateurs\n- Collaborer étroitement avec les développeurs\n\nProfil :\n- Maîtrise de Figma\n- Portfolio solide\n- Sensibilité au design et à l'expérience utilisateur",
                'contrat'         => 'CDI',
                'localisation'    => 'Casablanca',
                'niveau_etude'    => 'Bac+3',
                'salaire'         => '7 000 – 10 000 MAD',
                'duree'           => null,
                'statut'          => 'active',
                'date_expiration' => now()->addMonths(3),
            ],
        ];

        foreach ($offres as $offre) {
            Offre::create(array_merge($offre, [
                'date_publication' => now()->subDays(rand(1, 30)),
            ]));
        }
    }
}