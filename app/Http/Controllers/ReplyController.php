<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostForm;
use App\Models\Reply;
use App\Models\Thread;
use GuzzleHttp\Middleware;
use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate as FacadesGate;

class ReplyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }

    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(20);
    }

    public function store($channelId, Thread $thread, CreatePostForm $form)
    {
        return $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ])->load('owner');
    }

    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);


        try {
            $this->validate(request(), ['body' => 'required|spamfree']);

            $reply->update(request(['body']));
        } catch (\Exception) {
            return response('Sorry, your reply could not be save at this time.', 422);
        }
    }


    public function destroy(Reply $reply)
    {

        $this->authorize('update', $reply);

        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }
}
