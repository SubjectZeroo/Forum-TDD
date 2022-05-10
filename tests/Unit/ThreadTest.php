<?php

namespace Tests\Unit;

use App\Models\Thread;
use App\Models\User;
use App\Notifications\ThreadWasUpdated;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification as FacadesNotification;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    protected $thread;

    public function setUp(): void
    {
        parent::setUp();
        $this->thread = Thread::factory()->create();
    }

    /** @test */
    function a_thread_has_a_path()
    {
        $thread = Thread::factory()->create();

        $this->assertEquals("/threads/{$thread->channel->slug}/{$thread->slug}", $thread->path());
    }

    /** @test */
    function a_thread_has_a_creator()
    {

        $this->assertInstanceOf(User::class, $this->thread->creator);
    }

    /** @test */
    function a_thread_has_replies()
    {
        $this->assertInstanceOf(Collection::class, $this->thread->replies);
    }

    /** @test */
    public function a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);

        $this->assertCount(1, $this->thread->replies);
    }

    /** @test */
    function a_thread_notifies_all_registered_subscribers_when_a_reply_is_added()
    {

        FacadesNotification::fake();

        $this->signIn()->thread->subscribe()->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);

        FacadesNotification::assertSentTo(auth()->user(), ThreadWasUpdated::class);
    }

    /** @test */
    function a_thread_belongs_to_a_channel()
    {
        $thread = Thread::factory()->create();

        $this->assertInstanceOf('App\Models\Channel', $thread->channel);
    }

    /** @test */
    function a_thread_can_be_subscribed_to()
    {
        //Given we have a thread
        //And a authenticated user
        //When the user subscribe to the thread
        //Then we should be able to fetch all threads that the user has subscribed to.

        $thread = Thread::factory()->create();



        $thread->subscribe($userId = 1);



        $this->assertEquals(
            1,
            $thread->subscriptions()->where('user_id', $userId)->count()
        );
    }

    /** @test */
    function a_thread_can_be_unsubscribed_from()
    {

        //GIven we have a thread
        //and a user who is subscribed
        $thread = Thread::factory()->create();

        $thread->subscribe($userId = 1);

        $thread->unsubscribe($userId);

        $this->assertCount(0,  $thread->subscriptions);
    }

    /** @test */
    function it_knows_if_the_authenticated_is_subscribed_to_it()
    {
        $thread = Thread::factory()->create();


        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    /** @test */
    function a_thread_can_check_if_the_autheticated_user_has_read_all_replies()
    {
        $this->signIn();

        $thread = Thread::factory()->create();

        tap(auth()->user(), function ($user) use ($thread) {

            $this->assertTrue($thread->hasUpdatesFor($user));


            //Simulate that the user visited the thread

            $user->read($thread);

            $this->assertFalse($thread->hasUpdatesFor($user));
        });
    }

    // /** @test */
    // function a_thread_records_each_visit()
    // {


    //     $thread = Thread::factory()->make(['id' => 1]);

    //     $thread->visits()->reset();

    //     $this->assertSame(0, $thread->visits()->count());

    //     // $thread->resetVisit();

    //     $thread->visits()->record();

    //     $this->assertEquals(1, $thread->visits()->count());

    //     // $thread->recordVisit();

    //     // $this->assertEquals(2, $thread->visits());
    // }

    /** @test */
    function a_thread_may_be_locked()
    {
        $this->assertFalse($this->thread->locked);

        $this->thread->lock();

        $this->assertTrue($this->thread->locked);
    }

    /** @test */
    function a_threads_body_is_sanitized_automatically()
    {
        $thread = Thread::factory()->make(['body' => '<script>alert("bad")</script><p>This is okay.</p>']);

        $this->assertEquals('<p>This is okay.</p>', $thread->body);
    }
}
