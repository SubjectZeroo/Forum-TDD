<?php

namespace Tests\Feature;

use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;
    /** @test */
    function mentioned_users_in_a_reply_are_notified()
    {
        //Given i have a user, JonhDoe, whoe is signed in
        $john = User::factory()->create(['name' => 'JonhDoe']);
        $this->signIn($john);
        //And another use, JaneDoe,
        $jane = User::factory()->create(['name' => 'JaneDoe']);

        //If we have a thread

        $thread = Thread::factory()->create();
        //And JhonDoe replies and mentions @JaneDOe.

        $reply = Reply::factory()->make([
            'body' => '@JaneDoe look at this. Also @FrankDoe'
        ]);

        $this->json('post', $thread->path() . '/replies', $reply->toArray());

        //Them, Jane should be notified



        $this->assertCount(1, $jane->notifications);
    }
}
