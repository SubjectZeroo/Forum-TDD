<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\Reply;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = Thread::factory()->create();
    }

    /** @test */
    public function a_user_can_view_all_threads()
    {

        $response = $this->get('/threads');
        $response->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_read_a_single_thread()
    {

        $response = $this->get($this->thread->path());

        $response->assertSee($this->thread->title);
    }

    /**  @test */
    function a_user_can_filter_threads_according_to_a_channel()
    {

        $channel = Channel::factory()->create();
        $threadInChannel = Thread::factory()->create(['channel_id' => $channel->id]);
        $threadNotInChannel = Thread::factory()->create();

        $this->get('threads/' . $channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    function a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(User::factory()->create(['name' => 'JohnDoe']));

        $threadByJohn = Thread::factory()->create(['user_id' => auth()->id()]);
        $threadNotByJohn = Thread::factory()->create();

        $this->get('threads?by=JohnDoe')
            ->assertSee($threadByJohn->title)
            ->assertDontSee($threadNotByJohn->title);
    }

    /** @test */
    function a_user_can_filter_threads_by_popularity()
    {
        //Given we have three threads
        //with 2 replies, 3 replies, and 0 replies, respectively
        //when i filter all threads by popularity
        //Then they should be returned form most replies to least.

        $threadWithTwoReplies = Thread::factory()->create();

        Reply::factory(2)->create(['thread_id' => $threadWithTwoReplies->id]);

        $threadWithThreeReplies = Thread::factory()->create();

        Reply::factory(3)->create(['thread_id' => $threadWithThreeReplies->id]);


        $threadWithNoReplies = $this->thread;

        $response = $this->getJson('threads?popular=1')->json();


        $this->assertEquals([3, 2, 0], array_column($response['data'], 'replies_count'));
    }

    /** @test */
    function a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $thread = Thread::factory()->create();

        Reply::factory()->create(['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1, $response['data']);
    }

    /** @test */
    function a_user_can_request_all_replies_for_a_given_thread()
    {
        $thread = Thread::factory()->create();

        Reply::factory(2)->create(['thread_id' => $thread->id]);

        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['total']);
    }

    /** @test */
    function we_record_a_new_visit_each_time_the_thread_is_read()
    {
        $thread = Thread::factory()->create();

        $this->assertSame(0, $thread->visits);

        $this->call('GET', $thread->path());

        $this->assertEquals(1, $thread->fresh()->visits);
    }
}
