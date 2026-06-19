<?php

namespace Tests\Feature;

use App\Models\Cv;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class EntrepriseWorkflowTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function test_entreprise_can_create_update_and_delete_own_offer(): void
    {
        $entreprise = $this->makeEntreprise();
        $categorie = $this->makeCategorie();
        $competance = $this->makeCompetance();

        $this->actingAs($entreprise->utilisateur)
            ->post(route('entreprise.offres.store'), [
                'titre' => 'Full Stack Developer',
                'description' => 'Laravel Vue',
                'categorie_id' => $categorie->id,
                'contrat' => 'CDI',
                'localisation' => 'Casablanca',
                'niveau_etude' => 'Bac+5',
                'statut' => 'active',
                'competances' => [$competance->id],
            ])
            ->assertRedirect(route('entreprise.offres'));

        $this->assertDatabaseHas('offres', ['titre' => 'Full Stack Developer']);
        $offre = $entreprise->offres()->firstOrFail();
        $this->assertDatabaseHas('offre_competance', [
            'offre_id' => $offre->id,
            'competance_id' => $competance->id,
        ]);

        $this->actingAs($entreprise->utilisateur)
            ->put(route('entreprise.offres.update', $offre->id), [
                'titre' => 'Full Stack Senior',
                'description' => 'Laravel Vue Senior',
                'categorie_id' => $categorie->id,
                'contrat' => 'CDD',
                'localisation' => 'Rabat',
                'niveau_etude' => 'Bac+5',
                'statut' => 'brouillon',
                'competances' => [],
            ])
            ->assertRedirect(route('entreprise.offres'));

        $this->assertDatabaseHas('offres', [
            'id' => $offre->id,
            'titre' => 'Full Stack Senior',
            'statut' => 'brouillon',
        ]);

        $this->actingAs($entreprise->utilisateur)
            ->delete(route('entreprise.offres.supprimer', $offre->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('offres', ['id' => $offre->id]);
    }

    public function test_entreprise_cannot_manage_another_company_offer(): void
    {
        $entreprise = $this->makeEntreprise();
        $otherEntreprise = $this->makeEntreprise();
        $offre = $this->makeOffre($otherEntreprise);

        $this->actingAs($entreprise->utilisateur)
            ->delete(route('entreprise.offres.supprimer', $offre->id))
            ->assertNotFound();
    }

    public function test_entreprise_can_change_candidature_status_and_notify_candidate(): void
    {
        $entreprise = $this->makeEntreprise();
        $particulier = $this->makeParticulier();
        $offre = $this->makeOffre($entreprise);
        $candidature = $this->makeCandidature($particulier, $offre);

        $this->actingAs($entreprise->utilisateur)
            ->patch(route('entreprise.candidature.statut', $candidature->id), [
                'statut' => 'acceptee',
                'commentaire' => 'Bienvenue',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('candidatures', [
            'id' => $candidature->id,
            'statut' => 'acceptee',
            'commentaire' => 'Bienvenue',
        ]);
        $this->assertDatabaseHas('notifications', [
            'utilisateur_id' => $particulier->utilisateur_id,
            'type' => 'acceptee',
        ]);
    }

    public function test_entreprise_can_download_cv_only_for_own_candidature(): void
    {
        Storage::fake('local');

        $entreprise = $this->makeEntreprise();
        $otherEntreprise = $this->makeEntreprise();
        $particulier = $this->makeParticulier();
        $ownOffre = $this->makeOffre($entreprise);
        $otherOffre = $this->makeOffre($otherEntreprise);
        $ownCandidature = $this->makeCandidature($particulier, $ownOffre);
        $otherCandidature = $this->makeCandidature($particulier, $otherOffre);

        Storage::disk('local')->put('cvs/' . $particulier->id . '/cv.pdf', 'test');
        Cv::create([
            'particulier_id' => $particulier->id,
            'cv_path' => 'cvs/' . $particulier->id . '/cv.pdf',
        ]);

        $this->actingAs($entreprise->utilisateur)
            ->get(route('entreprise.candidature.cv', $ownCandidature->id))
            ->assertOk();

        $this->actingAs($entreprise->utilisateur)
            ->get(route('entreprise.candidature.cv', $otherCandidature->id))
            ->assertNotFound();
    }

    public function test_entreprise_matching_and_suggestion_pages_are_available(): void
    {
        $entreprise = $this->makeEntreprise();
        $particulier = $this->makeParticulier();
        $competance = $this->makeCompetance(['nom' => 'Laravel']);
        $this->attachCompetance($particulier, $competance);
        $offre = $this->makeOffre($entreprise, ['titre' => 'Laravel Recruit'], [$competance]);

        $this->actingAs($entreprise->utilisateur)
            ->get(route('entreprise.offres.matching', $offre->id))
            ->assertOk();

        $this->actingAs($entreprise->utilisateur)
            ->get(route('entreprise.offres.suggestions', $offre->id))
            ->assertOk()
            ->assertSee('Test Particulier');
    }
}
