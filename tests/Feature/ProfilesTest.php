<?php

namespace Tests\Feature;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ProfilesTest extends TestCase
{

    use DatabaseMigrations;
    /** @test */
    public function a_user_has_a_profile()
    {
        $user = User::factory()->create();

        $this->get("/profiles/{$user->name}")
            ->assertSee($user->name);
    }

    /** @test */

    public function profiles_display_all_threads_create_by_the_associated_user()
    {

        $this->signIn();


        $thread = Thread::factory()->create(['user_id' => auth()->id()]);

        $this->get("/profiles/" . auth()->user()->name)
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }
}
