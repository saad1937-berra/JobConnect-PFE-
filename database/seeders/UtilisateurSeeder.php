<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Utilisateur;
use App\Models\Particulier;

class UtilisateurSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ──────────────────────────────────────
        Utilisateur::create([
            'email'  => 'admin@jobconnect.ma',
            'pass'   => Hash::make('admin1234'),
            'nom'    => 'Admin',
            'prenom' => 'Super',
            'role'   => 'admin',
        ]);

        // ── Candidats (particuliers) ───────────────────
        $candidats = [
            ['prenom' => 'Youssef',  'nom' => 'Alami',    'email' => 'youssef@mail.ma'],
            ['prenom' => 'Fatima',   'nom' => 'Benali',   'email' => 'fatima@mail.ma'],
            ['prenom' => 'Karim',    'nom' => 'Chraibi',  'email' => 'karim@mail.ma'],
            ['prenom' => 'Sara',     'nom' => 'Douiri',   'email' => 'sara@mail.ma'],
            ['prenom' => 'Mehdi',    'nom' => 'El Fassi', 'email' => 'mehdi@mail.ma'],
        ];

        foreach ($candidats as $data) {
            $user = Utilisateur::create([
                'email'  => $data['email'],
                'pass'   => Hash::make('password123'),
                'nom'    => $data['nom'],
                'prenom' => $data['prenom'],
                'role'   => 'particulier',
            ]);

            Particulier::create([
                'utilisateur_id' => $user->id,
                'bio'            => "Professionnel motivé à la recherche de nouvelles opportunités.",
                'tel'            => '06' . rand(10000000, 99999999),
                'adresse'        => 'Casablanca, Maroc',
                'date_naissance' => now()->subYears(rand(22, 35))->format('Y-m-d'),
            ]);
        }
    }
}