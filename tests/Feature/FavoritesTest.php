<?php

namespace Tests\Feature;

use App\Models\Reply;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class FavoritesTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    function guest_can_not_favorite_anything()
    {
        $this->expectException(AuthenticationException::class);

        $this->withoutExceptionHandling();
        $this->post('replies/1/favorites')
            ->assertRedirect('login');
    }

    /** @test */
    public function an_authenticated_user_can_favorite_any_reply()
    {
        // if i post a "favorite" endpoint
        // it should be recored in the database.

        $this->signIn();

        $reply = Reply::factory()->create();

        $this->post('replies/' . $reply->id . '/favorites');

        $this->assertCount(1, $reply->fresh()->favorites);
    }

    /** @test */
    function an_authenticated_user_may_only_favorite_a_reply_once()
    {
        $this->signIn();

        $reply = Reply::factory()->create();

        try {
            $this->post('replies/' . $reply->id . '/favorites');
            $this->post('replies/' . $reply->id . '/favorites');
        } catch (\Exception $e) {
            $this->fail('Did not expected to insert the same set twice.');
        }


        $this->assertCount(1, $reply->fresh()->favorites);
    }


    /** @test */
    function an_authenticated_user_may_only_unfavorite_a_reply()
    {
        $this->signIn();

        $reply = Reply::factory()->create();


        // $this->post('replies/' . $reply->id . '/favorites');

        $reply->favorite();

        // $this->assertCount(1, $reply->fresh()->favorites);

        $this->delete('replies/' . $reply->id . '/favorites');

        $this->assertCount(0, $reply->favorites);
    }
}
