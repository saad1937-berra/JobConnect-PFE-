<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Services\MatchingService;
use App\Services\SuggestionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class MatchingSuggestionServiceTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function test_matching_uses_profile_when_no_cv_text_exists(): void
    {
        $particulier = $this->makeParticulier();
        $competance = $this->makeCompetance(['nom' => 'Laravel']);
        $this->attachCompetance($particulier, $competance);
        $offre = $this->makeOffre(null, ['titre' => 'Laravel Developer'], [$competance]);

        $matching = MatchingService::calculer(
            $particulier->load(['competances', 'cv']),
            $offre->load('competances')
        );

        $this->assertSame('profil', $matching['source']);
        $this->assertGreaterThan(0, $matching['score']);
    }

    public function test_matching_uses_cv_text_when_available(): void
    {
        $particulier = $this->makeParticulier();
        $competance = $this->makeCompetance(['nom' => 'Laravel']);
        $offre = $this->makeOffre(null, ['titre' => 'Laravel Developer'], [$competance]);

        Cv::create([
            'particulier_id' => $particulier->id,
            'cv_path' => 'cvs/test/cv.pdf',
            'cv_text' => 'Experience Laravel PHP Vue Casablanca',
        ]);

        $matching = MatchingService::calculer(
            $particulier->fresh()->load(['competances', 'cv']),
            $offre->load(['competances', 'categorie'])
        );

        $this->assertSame('cv', $matching['source']);
        $this->assertGreaterThan(0, $matching['score']);
        $this->assertContains('Laravel', $matching['criteres']['competences']['detail']);
    }

    public function test_suggestion_service_returns_relevant_offers_and_candidates(): void
    {
        $particulier = $this->makeParticulier();
        $competance = $this->makeCompetance(['nom' => 'Laravel']);
        $this->attachCompetance($particulier, $competance);
        $offre = $this->makeOffre(null, ['titre' => 'Laravel Backend'], [$competance]);

        $offres = SuggestionService::offresParCompetences($particulier->load(['competances', 'candidatures']), 5);
        $candidats = SuggestionService::candidatsParOffre($offre->load('competances'), 5);

        $this->assertTrue($offres->pluck('offre.id')->contains($offre->id));
        $this->assertTrue($candidats->pluck('particulier.id')->contains($particulier->id));
    }
}
