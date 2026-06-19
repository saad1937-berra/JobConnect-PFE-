<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Candidature;
use App\Models\Conversation;
use App\Models\Entreprise;
use App\Models\Offre;
use App\Models\Particulier;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MessageAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_entreprise_can_start_conversation_with_particulier(): void
    {
        $entreprise = $this->userWithProfile('entreprise');
        $particulier = $this->userWithProfile('particulier');

        $response = $this->actingAs($entreprise)->post(route('messages.start'), [
            'user_id' => $particulier->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('conversations', [
            'user_one_id' => min($entreprise->id, $particulier->id),
            'user_two_id' => max($entreprise->id, $particulier->id),
        ]);
        $this->assertDatabaseHas('messages', [
            'sender_id' => $entreprise->id,
        ]);
    }

    public function test_particulier_cannot_contact_entreprise_without_contact_or_accepted_candidature(): void
    {
        $particulier = $this->userWithProfile('particulier');
        $entreprise = $this->userWithProfile('entreprise');

        $this->actingAs($particulier)
            ->post(route('messages.start'), ['user_id' => $entreprise->id])
            ->assertForbidden();

        $this->assertDatabaseCount('conversations', 0);
    }

    public function test_particulier_can_reply_after_entreprise_contacts_them(): void
    {
        $entreprise = $this->userWithProfile('entreprise');
        $particulier = $this->userWithProfile('particulier');

        $this->actingAs($entreprise)->post(route('messages.start'), [
            'user_id' => $particulier->id,
            'body' => 'Bonjour',
        ]);

        $conversation = Conversation::between($entreprise, $particulier);

        $this->actingAs($particulier)
            ->post(route('messages.store', $conversation->id), ['body' => 'Merci'])
            ->assertRedirect();

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $particulier->id,
            'body' => 'Merci',
        ]);
    }

    public function test_particulier_can_contact_entreprise_after_accepted_candidature(): void
    {
        $entreprise = $this->userWithProfile('entreprise');
        $particulier = $this->userWithProfile('particulier');

        $this->acceptedCandidature($particulier, $entreprise);

        $this->actingAs($particulier)
            ->post(route('messages.start'), [
                'user_id' => $entreprise->id,
                'body' => 'Bonjour, merci pour lacceptation.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('messages', [
            'sender_id' => $particulier->id,
            'body' => 'Bonjour, merci pour lacceptation.',
        ]);
    }

    public function test_admin_can_view_private_conversations_and_warn_users(): void
    {
        $admin = $this->userWithProfile('admin');
        $entreprise = $this->userWithProfile('entreprise');
        $particulier = $this->userWithProfile('particulier');
        $conversation = Conversation::between($entreprise, $particulier);

        $this->actingAs($admin)
            ->get(route('messages.show', $conversation->id))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('messages.start'), [
                'user_id' => $particulier->id,
                'body' => 'Avertissement administratif',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('messages', [
            'sender_id' => $admin->id,
            'body' => 'Avertissement administratif',
        ]);
    }

    public function test_entreprise_can_report_particulier_to_admin(): void
    {
        $admin = $this->userWithProfile('admin');
        $entreprise = $this->userWithProfile('entreprise');
        $particulier = $this->userWithProfile('particulier');

        $this->actingAs($entreprise)->post(route('messages.start'), [
            'user_id' => $particulier->id,
            'body' => 'Bonjour',
        ]);

        $conversation = Conversation::between($entreprise, $particulier);

        $this->actingAs($entreprise)
            ->post(route('messages.report', $conversation->id), ['reason' => 'Messages insistants'])
            ->assertRedirect();

        $adminConversation = Conversation::between($admin, $entreprise);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $adminConversation->id,
            'sender_id' => $entreprise->id,
        ]);
    }

    public function test_particulier_still_cannot_message_another_particulier(): void
    {
        $first = $this->userWithProfile('particulier');
        $second = $this->userWithProfile('particulier');

        $this->actingAs($first)
            ->post(route('messages.start'), ['user_id' => $second->id])
            ->assertForbidden();

        $this->assertDatabaseCount('conversations', 0);
    }

    private function userWithProfile(string $role): Utilisateur
    {
        $user = Utilisateur::create([
            'email' => uniqid($role . '_') . '@example.test',
            'pass' => Hash::make('password'),
            'nom' => ucfirst($role),
            'prenom' => 'Test',
            'role' => $role,
            'date_inscription' => now(),
        ]);

        if ($role === 'particulier') {
            Particulier::create(['utilisateur_id' => $user->id]);
        }

        if ($role === 'entreprise') {
            Entreprise::create([
                'utilisateur_id' => $user->id,
                'nom' => 'Entreprise Test',
            ]);
        }

        return $user;
    }

    private function acceptedCandidature(Utilisateur $particulierUser, Utilisateur $entrepriseUser): void
    {
        $category = Categorie::create(['nom' => 'Informatique']);
        $offre = Offre::create([
            'entreprise_id' => $entrepriseUser->entreprise->id,
            'categorie_id' => $category->id,
            'titre' => 'Developpeur',
            'description' => 'Poste test',
            'statut' => 'active',
        ]);

        Candidature::create([
            'particulier_id' => $particulierUser->particulier->id,
            'offre_id' => $offre->id,
            'statut' => 'acceptee',
        ]);
    }
}
