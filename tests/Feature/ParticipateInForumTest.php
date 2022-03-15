<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParticipateInForumTest extends TestCase
{

    use DatabaseMigrations;


    /** @test */
    function unauthenticated_users_may_not_add_replies()
    {
        $this->withoutExceptionHandling();
        $this->expectException(AuthenticationException::class);



        $this->post('/threads/some-channel/1/replies', [])
            ->assertRedirect('/login');
    }

    /** @test */
    function an_authenticated_user_may_participate_in_forum_threads()
    {
        //Given we have authenticated user
        // And an existing thread
        // When the user adds a reply to the thread
        //Then their reply should be visible on the page

        // $user = User::factory()->create();

        $this->withoutExceptionHandling();

        $this->be($user =  User::factory()->create());

        $thread = Thread::factory()->create();

        $reply = Reply::factory()->make();

        $this->post($thread->path() . '/replies', $reply->toArray());

        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    /** @test */

    function a_reply_requires_a_body()
    {

        $this->signIn();

        $thread = Thread::factory()->create();

        $reply = Reply::factory()->make(['body' => null]);

        $this->post($thread->path() . '/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
    }

    /** @test */
    function unauthorized_users_cannot_delete_replies()
    {

        $this->withoutExceptionHandling();
        $this->expectException(AuthenticationException::class);

        $reply = Reply::factory()->create();

        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('login');


        $this->signIn()
            ->delete("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
    function authorized_user_can_delete_replies()
    {
        $this->signIn();
        $reply = Reply::factory()->create(['user_id' => auth()->id()]);

        $this->delete("/replies/{$reply->id}")->assertStatus(302);


        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);

        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
    }

    /** @test */
    function unauthorized_users_cannot_update_replies()
    {

        $this->withoutExceptionHandling();
        $this->expectException(AuthenticationException::class);

        $reply = Reply::factory()->create();

        $this->patch("/replies/{$reply->id}")
            ->assertRedirect('login');


        $this->signIn()
            ->patch("/replies/{$reply->id}")
            ->assertStatus(403);
    }

    /** @test */
    function authorized_users_can_update_replies()
    {
        $this->signIn();
        $reply = Reply::factory()->create(['user_id' => auth()->id()]);

        $updateReply = 'You been changed, fool.';

        $this->patch("/replies/{$reply->id}", ['body' =>
        $updateReply]);

        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => $updateReply]);
    }
}
