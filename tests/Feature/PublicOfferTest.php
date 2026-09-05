<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class PublicOfferTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function test_public_offer_index_shows_only_active_offers(): void
    {
        $entreprise = $this->makeEntreprise();
        $this->makeOffre($entreprise, ['titre' => 'Offre Active Laravel', 'statut' => 'active']);
        $this->makeOffre($entreprise, ['titre' => 'Offre Brouillon Cachee', 'statut' => 'brouillon']);

        $this->get(route('offres.index'))
            ->assertOk()
            ->assertSee('Offre Active Laravel')
            ->assertDontSee('Offre Brouillon Cachee');
    }

    public function test_public_offer_filters_by_search_location_contract_and_category(): void
    {
        $entreprise = $this->makeEntreprise();
        $category = $this->makeCategorie(['nom' => 'Tech']);

        $this->makeOffre($entreprise, [
            'titre' => 'Data Engineer',
            'categorie_id' => $category->id,
            'localisation' => 'Rabat',
            'contrat' => 'CDD',
        ]);
        $this->makeOffre($entreprise, [
            'titre' => 'Designer',
            'localisation' => 'Casablanca',
            'contrat' => 'CDI',
        ]);

        $this->get(route('offres.index', [
            'search' => 'Data',
            'localisation' => 'Rabat',
            'contrat' => 'CDD',
            'categorie_id' => $category->id,
        ]))
            ->assertOk()
            ->assertSee('Data Engineer')
            ->assertDontSee('Designer');
    }

    public function test_public_offer_show_displays_offer_details(): void
    {
        $offre = $this->makeOffre(null, ['titre' => 'Backend Developer']);

        $this->get(route('offres.show', $offre->id))
            ->assertOk()
            ->assertSee('Backend Developer');
    }

    public function test_public_pages_hide_blocked_company_and_its_offers(): void
    {
        $activeEntreprise = $this->makeEntreprise(['nom' => 'Visible Company']);
        $blockedEntreprise = $this->makeEntreprise(['nom' => 'Blocked Company'], ['role' => 'bloque']);

        $this->makeOffre($activeEntreprise, ['titre' => 'Visible Offer']);
        $blockedOffre = $this->makeOffre($blockedEntreprise, ['titre' => 'Hidden Blocked Offer']);

        $this->get(route('offres.index'))
            ->assertOk()
            ->assertSee('Visible Offer')
            ->assertSee('Visible Company')
            ->assertDontSee('Hidden Blocked Offer')
            ->assertDontSee('Blocked Company');

        $this->get(route('offres.show', $blockedOffre->id))
            ->assertNotFound();
    }

    public function test_public_pages_hide_pending_and_refused_company_offers(): void
    {
        $validatedEntreprise = $this->makeEntreprise(['nom' => 'Validated Company']);
        $pendingEntreprise = $this->makeEntreprise([
            'nom' => 'Pending Company',
            'statut_validation' => 'en_attente',
        ]);
        $refusedEntreprise = $this->makeEntreprise([
            'nom' => 'Refused Company',
            'statut_validation' => 'refusee',
        ]);
        $unverifiedEntreprise = $this->makeEntreprise(
            ['nom' => 'Unverified Company'],
            ['email_verified_at' => null]
        );

        $this->makeOffre($validatedEntreprise, ['titre' => 'Visible Validated Offer']);
        $pendingOffer = $this->makeOffre($pendingEntreprise, ['titre' => 'Hidden Pending Offer']);
        $refusedOffer = $this->makeOffre($refusedEntreprise, ['titre' => 'Hidden Refused Offer']);
        $unverifiedOffer = $this->makeOffre($unverifiedEntreprise, ['titre' => 'Hidden Unverified Offer']);

        $this->get(route('offres.index'))
            ->assertOk()
            ->assertSee('Visible Validated Offer')
            ->assertDontSee('Hidden Pending Offer')
            ->assertDontSee('Hidden Refused Offer')
            ->assertDontSee('Hidden Unverified Offer')
            ->assertDontSee('Pending Company')
            ->assertDontSee('Refused Company')
            ->assertDontSee('Unverified Company');

        $this->get(route('offres.show', $pendingOffer->id))->assertNotFound();
        $this->get(route('offres.show', $refusedOffer->id))->assertNotFound();
        $this->get(route('offres.show', $unverifiedOffer->id))->assertNotFound();
    }

    public function test_privacy_page_is_public(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('Politique de confidentialite')
            ->assertSee('Protection des CV');
    }
}
