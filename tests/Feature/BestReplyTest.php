<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class BestReplyTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_thread_creator_may_mark_any_reply_as_the_best_reply()
    {
        $this->signIn();
        $thread = Thread::factory()->create(['user_id' => auth()->id()]);

        $replies = Reply::factory(2)->create(['thread_id' => $thread->id]);

        $this->assertFalse($replies[1]->isBest());

        $this->postJson(route('best-replies.store', [$replies[1]->id]));

        $this->assertTrue($replies[1]->fresh()->isBest());
    }

    /** @test */
    function only_the_thread_creator_may_mark_a_best_reply()
    {
        $this->withExceptionHandling();
        $this->signIn();
        $thread = Thread::factory()->create(['user_id' => auth()->id()]);

        $replies = Reply::factory(2)->create(['thread_id' => $thread->id]);

        $this->signIn(User::factory()->create());

        $this->postJson(route('best-replies.store', [$replies[1]->id]))->assertStatus(403);

        $this->assertFalse($replies[1]->fresh()->isBest());
    }

    /** @test */
    // function if_a_best_reply_is_deleted_then_thread_is_properly_update_to_reflect_that()
    // {
    //     \DB::statement('PRAGMA foreign_keys=on;');
    //     $this->signIn();

    //     $reply = Reply::factory()->create(['user_id' => auth()->id()]);

    //     $reply->thread->markBestReply($reply);

    //     $this->delete(route('replies.destroy', $reply));

    //     $this->assertNull($reply->thread->fresh()->best_reply_id);
    // }
}
