<?php

namespace Tests\Feature;

use App\Inspections\Spam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SpamTest extends TestCase
{
    /** @test */
    public function it_checks_for_invalid_keywords()
    {

        //invalid keywords

        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply here'));

        $this->expectException('Exception');

        $spam->detect('yahoo customer support');
    }


    /** @test */
    function it_checks_for_any_key_being_held_down()
    {
        $spam = new Spam();

        $this->expectException('Exception');

        $spam->detect('hello world aaaaa');
    }
}
