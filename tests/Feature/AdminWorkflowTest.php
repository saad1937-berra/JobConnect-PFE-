<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class AdminWorkflowTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function test_admin_dashboard_and_lists_are_available(): void
    {
        $admin = $this->makeAdmin();
        $entreprise = $this->makeEntreprise();
        $this->makeOffre($entreprise);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.entreprises'))->assertOk();
        $this->actingAs($admin)->get(route('admin.utilisateurs'))->assertOk();
        $this->actingAs($admin)->get(route('admin.offres'))->assertOk();
    }

    public function test_admin_can_manage_categories(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'nom' => 'Data',
                'description' => 'Data jobs',
            ])
            ->assertRedirect();

        $category = \App\Models\Categorie::where('nom', 'Data')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category->id), [
                'nom' => 'Data Science',
                'description' => 'Updated',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'nom' => 'Data Science',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.categories.supprimer', $category->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_can_manage_competences(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->post(route('admin.competances.store'), [
                'nom' => 'Docker',
                'description' => 'Containers',
            ])
            ->assertRedirect();

        $competance = \App\Models\Competance::where('nom', 'Docker')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.competances.update', $competance->id), [
                'nom' => 'Docker Compose',
                'description' => 'Updated',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('competances', [
            'id' => $competance->id,
            'nom' => 'Docker Compose',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.competances.supprimer', $competance->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('competances', ['id' => $competance->id]);
    }

    public function test_admin_can_update_report_status(): void
    {
        $admin = $this->makeAdmin();
        $entreprise = $this->makeEntreprise();
        $particulier = $this->makeParticulier();
        $conversation = Conversation::between($entreprise->utilisateur, $particulier->utilisateur);

        $report = Report::create([
            'conversation_id' => $conversation->id,
            'reporter_id' => $entreprise->utilisateur_id,
            'reported_id' => $particulier->utilisateur_id,
            'reason' => 'Abus',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.signalements'))
            ->assertOk()
            ->assertSee('Abus');

        $this->actingAs($admin)
            ->patch(route('admin.signalements.update', $report->id), [
                'status' => 'traite',
                'admin_note' => 'Avertissement envoye',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'id' => $report->id,
            'status' => 'traite',
            'admin_note' => 'Avertissement envoye',
        ]);
        $this->assertNotNull($report->fresh()->resolved_at);
    }

    public function test_admin_blocking_user_revokes_tokens(): void
    {
        $admin = $this->makeAdmin();
        $particulier = $this->makeParticulier();
        $particulier->utilisateur->createToken('test-token');

        $this->actingAs($admin)
            ->patch(route('admin.utilisateurs.bloquer', $particulier->utilisateur_id))
            ->assertRedirect();

        $this->assertDatabaseHas('utilisateurs', [
            'id' => $particulier->utilisateur_id,
            'role' => 'bloque',
        ]);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
