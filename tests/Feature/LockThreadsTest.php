<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LockThreadsTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    function non_administrator_may_not_lock_threads()
    {
        $this->withExceptionHandling();
        $this->signIn();
        $thread = Thread::factory()->create(['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', $thread))->assertStatus(403);

        $this->assertFalse(!!$thread->fresh()->locked);
    }

    /** @test */
    function administrator_can_lock_threads()
    {
        $this->signIn(User::factory()->create(['name' => 'JohnDoe']));
        $thread = Thread::factory()->create(['user_id' => auth()->id()]);

        $this->post(route('locked-threads.store', $thread));

        $this->assertTrue(!!$thread->fresh()->locked);
    }

    /** @test */
    function administrator_can_unlock_threads()
    {
        $this->signIn(User::factory()->create(['name' => 'JohnDoe']));
        $thread = Thread::factory()->create(['user_id' => auth()->id(), 'locked' => true]);

        $this->delete(route('locked-threads.destroy', $thread));

        $this->assertFalse(!!$thread->fresh()->locked);
    }

    /** @test */
    public function once_locked_a_thread_may_not_receive_new_replies()
    {
        $this->signIn();
        $thread = Thread::factory()->create();

        $thread->lock();

        $this->post($thread->path() . '/replies', [
            'body' => 'Foobar',
            'user_id' => Auth()->id()
        ])->assertStatus(422);
    }
}
