<?php

namespace Tests\Feature;

use App\Models\Thread;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    // public function a_user_can_search_threads()
    // {
    //     config(['scout.driver' => 'algolia']);
    //     Thread::factory(2)->create([]);
    //     Thread::factory(2)->create(['body' => "A thread wiht the foobar term."]);

    //     do {
    //         sleep(.25);
    //         $results = $this->getJson("/threads/search?q=foobar")->json()['data'];
    //     } while (empty($results));

    //     $this->assertCount(2, $results);

    //     Thread::latest()->take(4)->unsearchable();
    // }
}
