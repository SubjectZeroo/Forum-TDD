<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->withExceptionHandling();
        $this->signIn();
    }

    /** @test */
    function unauthorized_users_may_not_update_threads()
    {
        $thread = Thread::factory()->create(['user_id' => User::factory()->create()->id]);

        $this->patch($thread->path(), [])->assertStatus(403);
    }

    /** @test */
    function a_thread_required_a_title_and_body_to_be_updated()
    {

        $thread = Thread::factory()->create(['user_id' => auth()->id()]);

        $this->patch($thread->path(), [
            'title' => 'Changed'
        ])->assertSessionHasErrors('body');

        $this->patch($thread->path(), [
            'body' => 'Changed'
        ])->assertSessionHasErrors('title');
    }



    /** @test */
    function a_thread_can_be_update_by_its_creator()
    {
        $thread = Thread::factory()->create(['user_id' => auth()->id()]);

        $this->patch($thread->path(), [
            'title' => 'Changed',
            'body' => 'Changed Body.'
        ]);


        tap($thread->fresh(), function ($thread) {
            // $this->assertEquals('Changed', $thread->title);
            $this->assertEquals('Changed Body.', $thread->body);
        });
    }
}
