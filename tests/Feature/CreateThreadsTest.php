<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function guest_may_not_create_threads()
    {
        $this->expectException(AuthenticationException::class);

        $this->withoutExceptionHandling();

        $this->get('threads/create')
            ->assertRedirect('/login');

        $this->post('/threads')
            ->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_users_must_first_confirm_their_email_address_before_creating_threads()
    {

        $this->publishThread([], User::factory()->unverified()->create())
            ->assertRedirect('email/verify');
    }
    /** @test */
    function guest_cannot_see_the_create_page()
    {
        $this->get('threads/create')
            ->assertRedirect('/login');
    }

    /** @test */
    function an_authenticated_user_can_create_new_forum_threads()
    {
        //Given we have signed in user
        // $this->actingAs(User::factory()->create());
        $this->signIn();
        //When we hit the endpoint to create a new thread
        $thread = Thread::factory()->make();

        $response = $this->post('/threads', $thread->toArray());

        //Then, when we visit the thread page
        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
        // we should see the new thread

    }

    /** @test */
    function a_thread_requires_a_title()
    {
        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    function a_thread_requires_a_valid_channel()
    {
        $channel = Channel::factory(2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    /** @test */
    function a_thread_requires_a_unique_slug()
    {
        $this->signIn();

        $thread = Thread::factory()->create(['title' => 'Foo title']);

        $this->assertEquals($thread->fresh()->slug, 'foo-title');

        $thread = $this->postJson(route('threads'), $thread->toArray())->json();

        $this->assertEquals("foo-title-{$thread['id']}", $thread['slug']);
    }

    /** @test */
    function a_thread_with_a_title_that_ends_in_a_number_should_generate_the_proper_slug()
    {
        $this->signIn();
        $thread = Thread::factory()->create(['title' => 'Some Title 24']);

        $thread = $this->postJson(route('threads'), $thread->toArray())->json();

        $this->assertEquals("some-title-24-{$thread['id']}", $thread['slug']);
    }

    /** @test */
    function unauthorized_users_may_not_delete_threads()
    {
        $this->expectException(AuthenticationException::class);

        $this->withoutExceptionHandling();

        $thread = Thread::factory()->create();

        $this->delete($thread->path())->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);
    }



    /** @test */
    function authorized_user_can_delete_threads()
    {
        $this->signIn();

        $thread = Thread::factory()->create(['user_id' => auth()->id()]);

        $reply = Reply::factory()->create(['thread_id' => $thread->id]);


        $response =  $this->json('DELETE', $thread->path());

        $response->assertStatus(204);

        $this->assertDatabaseMissing('threads', [
            'id' => $thread->id
        ]);

        $this->assertDatabaseMissing('replies', [
            'id' => $reply->id
        ]);

        // $this->assertDatabaseMissing('activities', [
        //     'subject_id' => $thread->id,
        //     'subject_type' => get_class($thread)
        // ]);

        // $this->assertDatabaseMissing('activities', [
        //     'subject_id' => $reply->id,
        //     'subject_type' => get_class($reply)
        // ]);

        $this->assertEquals(0, Activity::count());
    }


    protected function publishThread($attributes = [], $user = null)
    {
        // $this->withoutExceptionHandling();
        // $this->signIn();

        // $thread = Thread::factory()->make($overrides);

        // return $this->post('/threads', $thread->toArray());

        return $this->signIn($user)->post('threads', Thread::factory()->make($attributes)->toArray());
    }
}
