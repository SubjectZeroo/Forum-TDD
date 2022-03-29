<?php

namespace Tests\Unit;

use App\Models\Reply;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;


class ReplyTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    public function it_has_an_owner()
    {
        $reply = Reply::factory()->create();

        $this->assertInstanceOf('App\Models\User', $reply->owner);
    }

    /** @test */
    function it_knows_if_it_just_published()
    {
        $reply = Reply::factory()->create();

        $this->assertTrue($reply->wasJustPublished());

        $reply->created_at = Carbon::now()->subMonth();

        $this->assertFalse($reply->wasJustPublished());
    }
}
