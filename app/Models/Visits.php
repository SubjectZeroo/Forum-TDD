<?php

namespace App\Models;

use Illuminate\Support\Facades\Redis;

class Visits
{
    /**
     * Visits constructor
     */

    protected $thread;

    public function __construct($thread)
    {
        $this->thread = $thread;
    }

    public function record()
    {
        Redis::incr($this->cacheKey());

        return $this;
    }

    public function reset()
    {
        Redis::del($this->cacheKey());

        return $this;
    }

    public function count()
    {
        return Redis::get($this->cachekey()) ?? 0;
    }

    protected function cacheKey()
    {
        return "threads.{$this->thread->id}.visits";
    }
}
