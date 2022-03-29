<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscribeToThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_subscribe_to_threads()
    {

        $this->signIn();

        //Given we have a thread

        $thread =  Thread::factory()->create();


        //And the user subscribe to the thread

        $this->post($thread->path() . '/subscriptions');




        $this->assertCount(1,  $thread->fresh()->subscriptions);
    }


    /** @test */
    public function a_user_can_unsubscribe_to_threads()
    {
        $this->signIn();

        //Given we have a thread

        $thread =  Thread::factory()->create();


        $thread->subscribed();

        //And the user subscribe to the thread

        $this->delete($thread->path() . '/subscriptions');

        $this->assertCount(0, $thread->subscriptions);
    }
}
