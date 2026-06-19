<?php

namespace Database\Seeders;

use App\Models\Entreprise;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EntrepriseSeeder extends Seeder
{
    public function run(): void
    {
        $entreprises = [
            ['TechMaroc Solutions', 'Informatique & Tech', 'Casablanca Technopark, Casablanca', 'https://techmaroc.ma'],
            ['Atlas Group International', 'Finance & Comptabilite', 'Twin Center, Casablanca', 'https://atlasgroup.ma'],
            ['Sahra Digital Agency', 'Marketing & Communication', 'Rabat, Maroc', 'https://sahradigital.ma'],
            ['BuildMaroc Construction', 'Ingenierie & BTP', 'Marrakech, Maroc', null],
            ['Innovate Maroc', 'Informatique & Tech', 'Casablanca, Maroc', 'https://innovate.ma'],
            ['Nour RH Consulting', 'Ressources Humaines', 'Tanger, Maroc', 'https://nourrh.ma'],
            ['Maghreb Sales Partners', 'Commerce & Vente', 'Fes, Maroc', 'https://maghrebsales.ma'],
            ['MedCare Services', 'Sante & Medical', 'Rabat, Maroc', 'https://medcare.ma'],
            ['LegalTrust Morocco', 'Juridique', 'Casablanca, Maroc', 'https://legaltrust.ma'],
            ['Academia Pro Formation', 'Enseignement & Formation', 'Agadir, Maroc', 'https://academiapro.ma'],
        ];

        foreach ($entreprises as $index => [$nom, $secteur, $adresse, $siteWeb]) {
            $number = $index + 1;
            $slug = str_pad((string) $number, 2, '0', STR_PAD_LEFT);

            $user = Utilisateur::updateOrCreate(
                ['email' => "entreprise{$slug}@jobconnect.test"],
                [
                    'pass'   => Hash::make('password123'),
                    'nom'    => explode(' ', $nom)[0],
                    'prenom' => 'RH',
                    'role'   => 'entreprise',
                ]
            );

            Entreprise::updateOrCreate(
                ['utilisateur_id' => $user->id],
                [
                    'nom'         => $nom,
                    'secteur'     => $secteur,
                    'description' => "Entreprise marocaine specialisee en {$secteur}, avec des recrutements actifs sur JobConnect.",
                    'adresse'     => $adresse,
                    'site_web'    => $siteWeb,
                ]
            );
        }
    }
}
