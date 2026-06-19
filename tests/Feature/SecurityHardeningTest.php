<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\Particulier;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_reset_password_direct_flow_is_disabled(): void
    {
        $user = $this->userWithParticulier('particulier');
        $oldPassword = $user->pass;

        $this->postJson('/api/reset-pass', [
            'email' => $user->email,
            'pass' => 'new-password',
            'pass_confirmation' => 'new-password',
        ])->assertStatus(410);

        $this->assertSame($oldPassword, $user->fresh()->pass);
    }

    public function test_blocked_user_cannot_login_to_api(): void
    {
        $user = $this->userWithParticulier('bloque');

        $this->postJson('/api/login', [
            'email' => $user->email,
            'pass' => 'password123',
        ])->assertForbidden();
    }

    public function test_blocked_user_existing_api_token_is_rejected(): void
    {
        $user = $this->userWithParticulier('particulier');
        $token = $user->createToken('test')->plainTextToken;
        $user->update(['role' => 'bloque']);

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/notifications')
            ->assertForbidden();
    }

    public function test_uploaded_cv_is_stored_privately(): void
    {
        Storage::fake('local');
        Storage::fake('public');

        $user = $this->userWithParticulier('particulier');

        $this->actingAs($user)
            ->post(route('particulier.cv.upload'), [
                'cv' => UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf'),
            ])
            ->assertRedirect();

        $cv = Cv::firstOrFail();

        Storage::disk('local')->assertExists($cv->cv_path);
        Storage::disk('public')->assertMissing($cv->cv_path);
    }

    public function test_particulier_cannot_download_another_users_cv(): void
    {
        Storage::fake('local');

        $owner = $this->userWithParticulier('particulier');
        $other = $this->userWithParticulier('particulier');

        Storage::disk('local')->put('cvs/' . $owner->particulier->id . '/cv.pdf', 'test');

        $cv = Cv::create([
            'particulier_id' => $owner->particulier->id,
            'cv_path' => 'cvs/' . $owner->particulier->id . '/cv.pdf',
        ]);

        $this->actingAs($other)
            ->get(route('particulier.cv.download', $cv->id))
            ->assertNotFound();
    }

    private function userWithParticulier(string $role): Utilisateur
    {
        $user = Utilisateur::create([
            'email' => uniqid($role . '_') . '@example.test',
            'pass' => Hash::make('password123'),
            'nom' => 'Security',
            'prenom' => 'Test',
            'role' => $role,
            'date_inscription' => now(),
        ]);

        if ($role === 'particulier' || $role === 'bloque') {
            Particulier::create(['utilisateur_id' => $user->id]);
        }

        return $user;
    }
}
