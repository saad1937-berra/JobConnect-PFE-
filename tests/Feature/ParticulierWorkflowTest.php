<?php

namespace Tests\Feature;

use App\Models\Cv;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class ParticulierWorkflowTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function test_particulier_can_update_profile(): void
    {
        $particulier = $this->makeParticulier();

        $this->actingAs($particulier->utilisateur)
            ->put(route('particulier.profil.update'), [
                'bio' => 'Nouvelle bio',
                'tel' => '0611111111',
                'adresse' => 'Rabat, Maroc',
                'date_naissance' => '1999-01-01',
                'niveau_etude' => 'Bac+5',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('particuliers', [
            'id' => $particulier->id,
            'bio' => 'Nouvelle bio',
            'adresse' => 'Rabat, Maroc',
        ]);
    }

    public function test_particulier_can_add_and_remove_competence(): void
    {
        $particulier = $this->makeParticulier();
        $competance = $this->makeCompetance(['nom' => 'PHP']);

        $this->actingAs($particulier->utilisateur)
            ->post(route('particulier.competence.ajouter'), [
                'competance_id' => $competance->id,
                'niveau' => 'Expert',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('particulier_competance', [
            'particulier_id' => $particulier->id,
            'competance_id' => $competance->id,
            'niveau' => 'Expert',
        ]);

        $this->actingAs($particulier->utilisateur)
            ->delete(route('particulier.competence.supprimer', $competance->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('particulier_competance', [
            'particulier_id' => $particulier->id,
            'competance_id' => $competance->id,
        ]);
    }

    public function test_particulier_can_apply_once_and_entreprise_is_notified(): void
    {
        $particulier = $this->makeParticulier();
        $entreprise = $this->makeEntreprise();
        $offre = $this->makeOffre($entreprise, ['titre' => 'Poste Laravel']);

        $this->actingAs($particulier->utilisateur)
            ->post(route('particulier.postuler'), ['offre_id' => $offre->id])
            ->assertRedirect();

        $this->assertDatabaseHas('candidatures', [
            'particulier_id' => $particulier->id,
            'offre_id' => $offre->id,
            'statut' => 'en_attente',
        ]);
        $this->assertDatabaseHas('notifications', [
            'utilisateur_id' => $entreprise->utilisateur_id,
            'type' => 'candidature',
        ]);

        $this->actingAs($particulier->utilisateur)
            ->post(route('particulier.postuler'), ['offre_id' => $offre->id])
            ->assertRedirect();

        $this->assertDatabaseCount('candidatures', 1);
    }

    public function test_accepted_candidature_shows_contact_company_button(): void
    {
        $particulier = $this->makeParticulier();
        $entreprise = $this->makeEntreprise();
        $acceptedOffer = $this->makeOffre($entreprise, ['titre' => 'Accepted Role']);
        $pendingOffer = $this->makeOffre($entreprise, ['titre' => 'Pending Role']);

        $this->makeCandidature($particulier, $acceptedOffer, ['statut' => 'acceptee']);
        $this->makeCandidature($particulier, $pendingOffer, ['statut' => 'en_attente']);

        $response = $this->actingAs($particulier->utilisateur)
            ->get(route('particulier.candidatures'))
            ->assertOk()
            ->assertSee('Accepted Role')
            ->assertSee('Pending Role')
            ->assertSee("Contacter l'entreprise", false)
            ->assertSee('value="'.$entreprise->utilisateur_id.'"', false);

        $this->assertSame(1, substr_count($response->getContent(), "Contacter l'entreprise"));
    }

    public function test_candidatures_page_hides_blocked_company_offers(): void
    {
        $particulier = $this->makeParticulier();
        $visibleEntreprise = $this->makeEntreprise(['nom' => 'Visible Company']);
        $blockedEntreprise = $this->makeEntreprise(['nom' => 'Blocked Company'], ['role' => 'bloque']);

        $visibleOffer = $this->makeOffre($visibleEntreprise, ['titre' => 'Visible Application']);
        $blockedOffer = $this->makeOffre($blockedEntreprise, ['titre' => 'Hidden Application']);

        $this->makeCandidature($particulier, $visibleOffer);
        $this->makeCandidature($particulier, $blockedOffer);

        $this->actingAs($particulier->utilisateur)
            ->get(route('particulier.candidatures'))
            ->assertOk()
            ->assertSee('Visible Application')
            ->assertSee('Visible Company')
            ->assertDontSee('Hidden Application')
            ->assertDontSee('Blocked Company');
    }

    public function test_particulier_can_upload_and_download_own_cv(): void
    {
        Storage::fake('local');
        $particulier = $this->makeParticulier();

        $this->actingAs($particulier->utilisateur)
            ->post(route('particulier.cv.upload'), [
                'cv' => UploadedFile::fake()->create('cv.pdf', 20, 'application/pdf'),
            ])
            ->assertRedirect();

        $cv = Cv::firstOrFail();
        Storage::disk('local')->assertExists($cv->cv_path);

        $this->actingAs($particulier->utilisateur)
            ->get(route('particulier.cv.download', $cv->id))
            ->assertOk();
    }

    public function test_particulier_can_generate_cv_from_profile_data(): void
    {
        $particulier = $this->makeParticulier([
            'bio' => 'Developpeur Laravel motive',
            'tel' => '0611111111',
            'adresse' => 'Casablanca',
            'niveau_etude' => 'Bac+5',
        ], [
            'prenom' => 'Nada',
            'nom' => 'Hadji',
            'email' => 'nada@example.test',
        ]);
        $competance = $this->makeCompetance(['nom' => 'Laravel']);
        $this->attachCompetance($particulier, $competance, 'Expert');

        $this->actingAs($particulier->utilisateur)
            ->get(route('particulier.cv.generate'))
            ->assertOk()
            ->assertSee('Nada Hadji')
            ->assertSee('nada@example.test')
            ->assertSee('Developpeur Laravel motive')
            ->assertSee('Laravel')
            ->assertSee('Telecharger en PDF');
    }

    public function test_particulier_matching_and_suggestions_pages_are_available(): void
    {
        $particulier = $this->makeParticulier();
        $competance = $this->makeCompetance(['nom' => 'Laravel']);
        $this->attachCompetance($particulier, $competance);
        $offre = $this->makeOffre(null, ['titre' => 'Laravel Engineer'], [$competance]);

        $this->actingAs($particulier->utilisateur)
            ->get(route('particulier.suggestions'))
            ->assertOk()
            ->assertSee('Laravel Engineer');

        $this->actingAs($particulier->utilisateur)
            ->get(route('particulier.matching'))
            ->assertOk();

        $this->actingAs($particulier->utilisateur)
            ->get(route('particulier.matching.score', $offre->id))
            ->assertOk();
    }
}
