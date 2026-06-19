<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
