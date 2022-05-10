<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use Illuminate\Http\Request;

class ThreadLockedController extends Controller
{
    public function store(Thread $thread)
    {
        $thread->lock();
    }

    public function destroy(Thread $thread)
    {
        $thread->unlock();
    }
}
