<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;
use App\Models\Entreprise;

class EntrepriseSeeder extends Seeder
{
    public function run(): void
    {
        $entreprises = [
            [
                'email'       => 'rh@techmaroc.ma',
                'nom_user'    => 'TechMaroc',
                'nom'         => 'TechMaroc Solutions',
                'secteur'     => 'Informatique & Tech',
                'description' => 'Leader marocain des solutions digitales et du développement logiciel sur mesure.',
                'adresse'     => 'Casablanca Technopark, Casablanca',
                'site_web'    => 'https://techmaroc.ma',
            ],
            [
                'email'       => 'rh@atlasgroup.ma',
                'nom_user'    => 'AtlasGroup',
                'nom'         => 'Atlas Group International',
                'secteur'     => 'Finance & Comptabilité',
                'description' => 'Groupe financier opérant dans le conseil, l\'audit et la gestion de patrimoine.',
                'adresse'     => 'Twin Center, Casablanca',
                'site_web'    => 'https://atlasgroup.ma',
            ],
            [
                'email'       => 'emploi@sahradigital.ma',
                'nom_user'    => 'SahraDigital',
                'nom'         => 'Sahra Digital Agency',
                'secteur'     => 'Marketing & Communication',
                'description' => 'Agence digitale spécialisée en marketing de contenu, SEO et gestion des réseaux sociaux.',
                'adresse'     => 'Rabat, Maroc',
                'site_web'    => 'https://sahradigital.ma',
            ],
            [
                'email'       => 'career@buildmaroc.ma',
                'nom_user'    => 'BuildMaroc',
                'nom'         => 'BuildMaroc Construction',
                'secteur'     => 'Ingénierie & BTP',
                'description' => 'Entreprise de construction et de génie civil avec 20 ans d\'expérience au Maroc.',
                'adresse'     => 'Marrakech, Maroc',
                'site_web'    => null,
            ],
            [
                'email'       => 'jobs@innovate.ma',
                'nom_user'    => 'InnovateMa',
                'nom'         => 'Innovate Maroc',
                'secteur'     => 'Informatique & Tech',
                'description' => 'Startup marocaine spécialisée en intelligence artificielle et data science.',
                'adresse'     => 'Casablanca, Maroc',
                'site_web'    => 'https://innovate.ma',
            ],
        ];

        foreach ($entreprises as $data) {
            $user = Utilisateur::create([
                'email'  => $data['email'],
                'pass'   => Hash::make('password123'),
                'nom'    => $data['nom_user'],
                'prenom' => 'RH',
                'role'   => 'entreprise',
            ]);

            Entreprise::create([
                'utilisateur_id' => $user->id,
                'nom'            => $data['nom'],
                'secteur'        => $data['secteur'],
                'description'    => $data['description'],
                'adresse'        => $data['adresse'],
                'site_web'       => $data['site_web'],
            ]);
        }
    }
}