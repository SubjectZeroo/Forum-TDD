<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;


    public function setUp(): void
    {
        parent::setUp();

        $this->signIn();
    }

    /** @test */
    function a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_that_is_not_by_the_current_user()
    {

        $thread = Thread::factory()->create()->subscribe();

        $this->assertCount(0, auth()->user()->notifications);

        // //Then, each time a new reply is  left...

        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'Some Reply here'
        ]);

        // //A notification should be prepared for the user

        $this->assertCount(0, auth()->user()->fresh()->notifications);


        $thread->addReply([
            'user_id' => User::factory()->create()->id,
            'body' => 'Some Reply here'
        ]);

        $this->assertCount(1, auth()->user()->fresh()->notifications);
    }

    /** @test */
    function a_user_can_fetch_their_unread_notifications()
    {


        $thread = Thread::factory()->create()->subscribe();

        $thread->addReply([
            'user_id' => User::factory()->create()->id,
            'body' => 'Some Reply here'
        ]);

        $user = auth()->user();

        $response = $this->getJson("/profiles/{$user->name}/notifications")->json();

        $this->assertCount(1, $response);
    }

    /** @test */
    function a_user_can_mark_a_notification_as_read()
    {

        $thread = Thread::factory()->create()->subscribe();

        $thread->addReply([
            'user_id' => User::factory()->create()->id,
            'body' => 'Some Reply here'
        ]);

        $user = auth()->user();

        $this->assertCount(1, $user->unreadNotifications);

        $notificationId = $user->unreadNotifications->first()->id;

        $this->delete("/profiles/{$user->name}/notifications/{$notificationId}");

        $this->assertCount(0, $user->fresh()->unreadNotifications);
    }
}
