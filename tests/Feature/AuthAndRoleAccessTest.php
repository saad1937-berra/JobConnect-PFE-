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

    public function test_login_page_is_available_to_guests_without_caching(): void
    {
        $response = $this->get(route('login'))->assertOk()->assertSee('Se connecter');
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $response->assertHeader('Pragma', 'no-cache');
    }

    public function test_authenticated_users_cannot_view_or_submit_login(): void
    {
        foreach (['particulier', 'entreprise', 'admin'] as $role) {
            $user = $this->makeUser($role);
            $this->actingAs($user)->get(route('login'))->assertRedirect(route('home'));
            $this->post(route('login'), [
                'email' => 'other@example.test', 'pass' => 'incorrect',
            ])->assertRedirect(route('home'));
            $this->assertAuthenticatedAs($user);
        }
    }

    public function test_login_is_available_again_after_logout(): void
    {
        $this->actingAs($this->makeUser('particulier'))
            ->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
        $this->get(route('login'))->assertOk();
    }

    public function test_guest_is_redirected_from_protected_pages(): void
    {
        $this->get(route('particulier.profil'))->assertRedirect(route('login'));
        $this->get(route('entreprise.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('messages.index'))->assertRedirect(route('login'));
    }

    public function test_logout_prevents_returning_to_protected_pages_for_every_role(): void
    {
        $accounts = [
            [$this->makeParticulier()->utilisateur, 'particulier.profil'],
            [$this->makeEntreprise()->utilisateur, 'entreprise.dashboard'],
            [$this->makeAdmin(), 'admin.dashboard'],
        ];

        foreach ($accounts as [$user, $page]) {
            $response = $this->actingAs($user)->get(route($page))->assertOk();
            $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
            $response->assertSee("window.addEventListener('pageshow'", false);

            $home = $this->get(route('home'))->assertOk();
            $this->assertStringContainsString('no-store', $home->headers->get('Cache-Control'));

            $this->post(route('logout'))->assertRedirect(route('home'));
            $this->assertGuest();
            $this->get(route($page))->assertRedirect(route('login'));
            $this->get(route('home'))->assertOk()->assertSee('Connexion')->assertDontSee('Déconnexion');
        }
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
