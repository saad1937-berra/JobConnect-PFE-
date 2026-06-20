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

    public function test_privacy_page_is_public(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('Politique de confidentialite')
            ->assertSee('Protection des CV');
    }
}
