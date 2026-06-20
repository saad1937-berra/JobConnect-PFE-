<?php

namespace Tests\Feature;

use App\Mail\JobConnectNotificationMail;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Concerns\CreatesTestData;
use Tests\TestCase;

class NotificationWorkflowTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function test_user_can_mark_notifications_as_read(): void
    {
        $particulier = $this->makeParticulier();
        $notification = $particulier->utilisateur->notifications()->create([
            'type' => 'message',
            'message' => 'Notification test',
        ]);

        $this->actingAs($particulier->utilisateur)
            ->patch(route('notifications.lire', $notification->id))
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->date_lecture);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $particulier = $this->makeParticulier();
        $particulier->utilisateur->notifications()->create(['type' => 'message', 'message' => 'A']);
        $particulier->utilisateur->notifications()->create(['type' => 'message', 'message' => 'B']);

        $this->actingAs($particulier->utilisateur)
            ->patch(route('notifications.lire.tout'))
            ->assertRedirect();

        $this->assertSame(0, $particulier->utilisateur->notifications()->whereNull('date_lecture')->count());
    }

    public function test_notification_service_sends_email_to_recipient(): void
    {
        Mail::fake();
        $particulier = $this->makeParticulier([], [
            'email' => 'candidate@example.test',
            'prenom' => 'Candidate',
            'nom' => 'Test',
        ]);

        NotificationService::envoyer(
            $particulier->utilisateur_id,
            'message',
            'Nouveau message recu.'
        );

        $this->assertDatabaseHas('notifications', [
            'utilisateur_id' => $particulier->utilisateur_id,
            'type' => 'message',
            'message' => 'Nouveau message recu.',
        ]);

        Mail::assertSent(JobConnectNotificationMail::class, function ($mail) {
            return $mail->hasTo('candidate@example.test')
                && $mail->type === 'message'
                && $mail->notificationMessage === 'Nouveau message recu.';
        });
    }

    public function test_admin_api_notification_sends_email(): void
    {
        Mail::fake();
        $admin = $this->makeAdmin();
        $entreprise = $this->makeEntreprise([], ['email' => 'company@example.test']);
        $token = $admin->createToken('admin')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/admin/notifications', [
                'utilisateur_id' => $entreprise->utilisateur_id,
                'type' => 'admin',
                'message' => 'Avertissement administratif.',
            ])
            ->assertCreated();

        Mail::assertSent(JobConnectNotificationMail::class, function ($mail) {
            return $mail->hasTo('company@example.test')
                && $mail->type === 'admin'
                && $mail->notificationMessage === 'Avertissement administratif.';
        });
    }
}
