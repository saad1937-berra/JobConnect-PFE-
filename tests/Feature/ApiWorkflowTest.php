<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class ApiWorkflowTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function test_api_register_and_login_return_expected_payloads(): void
    {
        $this->postJson('/api/register', [
            'email' => 'api-user@example.test',
            'pass' => 'password123',
            'pass_confirmation' => 'password123',
            'nom' => 'Api',
            'prenom' => 'User',
            'role' => 'particulier',
        ])->assertCreated()
            ->assertJsonPath('user.email', 'api-user@example.test');

        $this->postJson('/api/login', [
            'email' => 'api-user@example.test',
            'pass' => 'password123',
        ])->assertOk()
            ->assertJsonStructure(['access_token', 'token_type', 'user']);

        $this->postJson('/api/register', [
            'email' => 'api-company@example.test',
            'pass' => 'password123',
            'pass_confirmation' => 'password123',
            'nom' => 'Api Company',
            'prenom' => 'Owner',
            'role' => 'entreprise',
        ])->assertCreated();

        $this->assertDatabaseHas('entreprises', [
            'nom' => 'Api Company',
            'statut_validation' => 'en_attente',
        ]);
    }

    public function test_api_role_routes_are_protected(): void
    {
        $particulier = $this->makeParticulier();
        $token = $particulier->utilisateur->createToken('api')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/particulier/profil')
            ->assertOk();

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/entreprise/dashboard')
            ->assertForbidden();
    }

    public function test_api_particulier_can_apply_to_offer_once(): void
    {
        $particulier = $this->makeParticulier();
        $offre = $this->makeOffre();
        $token = $particulier->utilisateur->createToken('api')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/particulier/postuler', ['offre_id' => $offre->id])
            ->assertCreated();

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/particulier/postuler', ['offre_id' => $offre->id])
            ->assertStatus(409);
    }

    public function test_api_pending_enterprise_cannot_publish_offer(): void
    {
        $entreprise = $this->makeEntreprise(['statut_validation' => 'en_attente']);
        $categorie = $this->makeCategorie();
        $token = $entreprise->utilisateur->createToken('api')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/entreprise/offres', [
                'titre' => 'API Pending Offer',
                'description' => 'Should not be created',
                'categorie_id' => $categorie->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('offres', ['titre' => 'API Pending Offer']);
    }

    public function test_api_admin_blocking_revokes_existing_tokens(): void
    {
        $admin = $this->makeAdmin();
        $particulier = $this->makeParticulier();
        $particulier->utilisateur->createToken('old-token');
        $adminToken = $admin->createToken('admin')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $adminToken)
            ->patchJson('/api/admin/utilisateurs/' . $particulier->utilisateur_id . '/bloquer')
            ->assertOk();

        $this->assertDatabaseHas('utilisateurs', [
            'id' => $particulier->utilisateur_id,
            'role' => 'bloque',
        ]);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $particulier->utilisateur_id,
        ]);
    }

    public function test_api_admin_can_unblock_user(): void
    {
        $admin = $this->makeAdmin();
        $entreprise = $this->makeEntreprise([], ['role' => 'bloque']);
        $adminToken = $admin->createToken('admin')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $adminToken)
            ->patchJson('/api/admin/utilisateurs/' . $entreprise->utilisateur_id . '/debloquer')
            ->assertOk()
            ->assertJsonPath('role', 'entreprise');

        $this->assertDatabaseHas('utilisateurs', [
            'id' => $entreprise->utilisateur_id,
            'role' => 'entreprise',
        ]);
    }
}
