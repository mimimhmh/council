<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->signIn();
    }

    /**
     * @test
     */
    public function a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_that_is_not_by_current_user(
    )
    {

        // Given we have a thread
        $thread = create(Thread::class)->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        // Then, each time a new reply is left...
        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'Some reply here',
        ]);

        // A notification should be prepared for the user
        $this->assertCount(0, auth()->user()->fresh()->notifications);

        // A reply created by other user
        $thread->addReply([
            'user_id' => create(User::class)->id,
            'body' => 'Some reply here',
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /**
     * @test
     */
    public function a_user_can_fetch_their_unread_notifications()
    {

        create(DatabaseNotification::class);

        $this->assertCount(1, $this->getJson("/profiles/".auth()->user()."/notifications")->json());
    }

    /**
     * @test
     */
    public function a_user_can_mark_a_notification_as_read()
    {

        create(DatabaseNotification::class);

        tap(auth()->user(), function ($user) {
            $this->assertCount(1, $user->unreadNotifications);

            $notificationId = $user->unreadNotifications->first()->id;

            $this->delete("/profiles/{$user->name}/notifications/{$notificationId}");

            $this->assertCount(0, $user->refresh()->unreadNotifications);
        });
    }
}
