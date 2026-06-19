<?php

namespace Tests\Feature\Concerns;

use App\Models\Categorie;
use App\Models\Candidature;
use App\Models\Competance;
use App\Models\Entreprise;
use App\Models\Offre;
use App\Models\Particulier;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

trait CreatesTestData
{
    protected function makeUser(string $role = 'particulier', array $attributes = []): Utilisateur
    {
        return Utilisateur::create(array_merge([
            'email' => uniqid($role . '_') . '@example.test',
            'pass' => Hash::make('password123'),
            'nom' => ucfirst($role),
            'prenom' => 'Test',
            'role' => $role,
            'date_inscription' => now(),
        ], $attributes));
    }

    protected function makeAdmin(array $attributes = []): Utilisateur
    {
        return $this->makeUser('admin', $attributes);
    }

    protected function makeParticulier(array $profile = [], array $userAttributes = []): Particulier
    {
        $user = $this->makeUser('particulier', $userAttributes);

        return Particulier::create(array_merge([
            'utilisateur_id' => $user->id,
            'bio' => 'Profil de test',
            'tel' => '0600000000',
            'adresse' => 'Casablanca, Maroc',
            'date_naissance' => now()->subYears(25)->toDateString(),
            'niveau_etude' => 'Bac+5',
        ], $profile));
    }

    protected function makeEntreprise(array $profile = [], array $userAttributes = []): Entreprise
    {
        $user = $this->makeUser('entreprise', $userAttributes);

        return Entreprise::create(array_merge([
            'utilisateur_id' => $user->id,
            'nom' => 'Entreprise Test',
            'secteur' => 'IT',
            'description' => 'Entreprise de test',
            'adresse' => 'Casablanca, Maroc',
            'site_web' => 'https://example.test',
        ], $profile));
    }

    protected function makeCategorie(array $attributes = []): Categorie
    {
        return Categorie::create(array_merge([
            'nom' => uniqid('Categorie '),
            'description' => 'Categorie de test',
        ], $attributes));
    }

    protected function makeCompetance(array $attributes = []): Competance
    {
        return Competance::create(array_merge([
            'nom' => uniqid('Laravel '),
            'description' => 'Competence de test',
        ], $attributes));
    }

    protected function makeOffre(?Entreprise $entreprise = null, array $attributes = [], array $competances = []): Offre
    {
        $entreprise ??= $this->makeEntreprise();
        $categorie = $attributes['categorie_id'] ?? $this->makeCategorie()->id;

        $offre = Offre::create(array_merge([
            'entreprise_id' => $entreprise->id,
            'categorie_id' => $categorie,
            'titre' => 'Developpeur Laravel',
            'description' => 'Developpement web Laravel Vue',
            'date_publication' => now(),
            'date_expiration' => now()->addMonth(),
            'contrat' => 'CDI',
            'duree' => null,
            'localisation' => 'Casablanca',
            'niveau_etude' => 'Bac+5',
            'statut' => 'active',
            'salaire' => '10000 MAD',
        ], $attributes));

        if (!empty($competances)) {
            $ids = collect($competances)->map(fn($competance) => $competance instanceof Competance ? $competance->id : $competance)->all();
            $offre->competances()->sync($ids);
        }

        return $offre;
    }

    protected function makeCandidature(Particulier $particulier, Offre $offre, array $attributes = []): Candidature
    {
        return Candidature::create(array_merge([
            'particulier_id' => $particulier->id,
            'offre_id' => $offre->id,
            'statut' => 'en_attente',
            'commentaire' => null,
        ], $attributes));
    }

    protected function attachCompetance(Particulier $particulier, Competance $competance, string $niveau = 'Expert'): void
    {
        $particulier->competances()->syncWithoutDetaching([
            $competance->id => ['niveau' => $niveau],
        ]);
    }
}
