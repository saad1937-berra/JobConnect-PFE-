<?php

namespace Database\Seeders;

use App\Models\Competance;
use App\Models\Particulier;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UtilisateurSeeder extends Seeder
{
    public function run(): void
    {
        Utilisateur::updateOrCreate(
            ['email' => 'admin@jobconnect.ma'],
            [
                'pass'   => Hash::make('admin1234'),
                'nom'    => 'Admin',
                'prenom' => 'Super',
                'role'   => 'admin',
            ]
        );

        $prenoms = [
            'Youssef', 'Fatima', 'Karim', 'Sara', 'Mehdi', 'Amina', 'Nadia', 'Omar', 'Hajar', 'Ayoub',
            'Imane', 'Hamza', 'Salma', 'Anas', 'Meryem', 'Zakaria', 'Lina', 'Reda', 'Noura', 'Ilyas',
        ];

        $noms = [
            'Alami', 'Benali', 'Chraibi', 'Douiri', 'El Fassi', 'Mansouri', 'Berrada', 'Tazi', 'Idrissi', 'Amrani',
            'Lahlou', 'Alaoui', 'Raji', 'Kabbaj', 'Bennis', 'Naciri', 'Sbai', 'Belkacem', 'Zerouali', 'Mernissi',
        ];

        $villes = ['Casablanca', 'Rabat', 'Marrakech', 'Tanger', 'Fes', 'Agadir', 'Meknes', 'Oujda', 'Kenitra', 'Remote'];
        $niveauxEtude = ['Bac', 'Bac+2', 'Bac+3', 'Bac+4', 'Bac+5', 'Doctorat'];
        $competanceIds = Competance::pluck('id')->values();

        for ($i = 1; $i <= 100; $i++) {
            $prenom = $prenoms[($i - 1) % count($prenoms)];
            $nom = $noms[(int) floor(($i - 1) / count($prenoms)) % count($noms)];
            $email = 'candidat' . str_pad((string) $i, 3, '0', STR_PAD_LEFT) . '@jobconnect.test';

            $user = Utilisateur::updateOrCreate(
                ['email' => $email],
                [
                    'pass'   => Hash::make('password123'),
                    'nom'    => $nom,
                    'prenom' => $prenom,
                    'role'   => 'particulier',
                ]
            );

            $particulier = Particulier::updateOrCreate(
                ['utilisateur_id' => $user->id],
                [
                    'bio'            => 'Professionnel motive avec une experience adaptee aux besoins du marche.',
                    'tel'            => '06' . str_pad((string) (10000000 + $i), 8, '0', STR_PAD_LEFT),
                    'adresse'        => $villes[$i % count($villes)] . ', Maroc',
                    'date_naissance' => now()->subYears(22 + ($i % 18))->subDays($i)->format('Y-m-d'),
                    'niveau_etude'   => $niveauxEtude[$i % count($niveauxEtude)],
                ]
            );

            if ($competanceIds->isNotEmpty()) {
                $selected = $competanceIds->shuffle()->take(rand(4, min(8, $competanceIds->count())));
                $particulier->competances()->sync($selected->all());
            }
        }
    }
}
