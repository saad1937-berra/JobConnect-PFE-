<?php

namespace Tests\Feature;

use App\Mail\VerifyEmailMail;
use App\Services\EmailVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class AuthAndRoleAccessTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function test_guest_is_redirected_from_protected_pages(): void
    {
        $this->get(route('particulier.profil'))->assertRedirect(route('login'));
        $this->get(route('entreprise.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('messages.index'))->assertRedirect(route('login'));
    }

    public function test_registration_creates_particulier_profile(): void
    {
        Mail::fake();

        $this->post('/register', [
            'email' => 'new-candidat@example.test',
            'pass' => 'password123',
            'pass_confirmation' => 'password123',
            'nom' => 'Candidat',
            'prenom' => 'Nouveau',
            'role' => 'particulier',
        ])->assertRedirect(route('home'));

        $this->assertDatabaseHas('utilisateurs', [
            'email' => 'new-candidat@example.test',
            'role' => 'particulier',
            'email_verified_at' => null,
        ]);
        $this->assertDatabaseCount('particuliers', 1);
        Mail::assertSent(VerifyEmailMail::class, fn($mail) => $mail->hasTo('new-candidat@example.test'));
    }

    public function test_registration_creates_entreprise_profile(): void
    {
        Mail::fake();

        $this->post('/register', [
            'email' => 'new-company@example.test',
            'pass' => 'password123',
            'pass_confirmation' => 'password123',
            'nom' => 'Company',
            'prenom' => 'Owner',
            'role' => 'entreprise',
        ])->assertRedirect(route('home'));

        $this->assertDatabaseHas('utilisateurs', [
            'email' => 'new-company@example.test',
            'role' => 'entreprise',
            'email_verified_at' => null,
        ]);
        $this->assertDatabaseHas('entreprises', [
            'nom' => 'Company',
            'statut_validation' => 'en_attente',
        ]);
        Mail::assertSent(VerifyEmailMail::class, fn($mail) => $mail->hasTo('new-company@example.test'));
    }

    public function test_email_verification_link_marks_user_as_verified(): void
    {
        $user = $this->makeUser('particulier', ['email_verified_at' => null]);

        $this->get(EmailVerificationService::verificationUrl($user))
            ->assertRedirect(route('home'));

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_login_rejects_blocked_user(): void
    {
        $this->makeUser('bloque', ['email' => 'blocked@example.test']);

        $this->post('/login', [
            'email' => 'blocked@example.test',
            'pass' => 'password123',
        ])->assertSessionHasErrors('email');
    }

    public function test_roles_can_only_access_their_own_sections(): void
    {
        $particulier = $this->makeParticulier();
        $entreprise = $this->makeEntreprise();
        $admin = $this->makeAdmin();

        $this->actingAs($particulier->utilisateur)->get(route('particulier.profil'))->assertOk();
        $this->actingAs($particulier->utilisateur)->get(route('entreprise.dashboard'))->assertForbidden();
        $this->actingAs($particulier->utilisateur)->get(route('admin.dashboard'))->assertForbidden();

        $this->actingAs($entreprise->utilisateur)->get(route('entreprise.dashboard'))->assertOk();
        $this->actingAs($entreprise->utilisateur)->get(route('particulier.profil'))->assertForbidden();
        $this->actingAs($entreprise->utilisateur)->get(route('admin.dashboard'))->assertForbidden();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('particulier.profil'))->assertForbidden();
        $this->actingAs($admin)->get(route('entreprise.dashboard'))->assertForbidden();
    }
}
