<?php

namespace App\Models;

use App\Events\ThreadHasNewReply;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PDO;

class Thread extends Model
{
    use HasFactory, RecordsActivity;

    protected $guarded = [];

    protected $with = ['creator', 'channel'];

    protected $appends = ['isSubscribedTo'];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });
    }



    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
        // return '/threads/' . $this->channel->slug . '/' . $this->id;
    }



    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
    // public function getReplyCountAttribute()
    // {
    //     return $this->replies()->count();
    // }

    public function addReply($reply)
    {

        $reply =  $this->replies()->create($reply);

        //Prepare notifications for all subscribers.
        $this->notifySubcribers($reply);


        return $reply;
    }

    public function notifySubcribers($reply)
    {
        $this->subscriptions->where('user_id', '!=', $reply->user_id)->each->notify($reply);
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function hasUpdatesFor($user)
    {
        //Look in the cache for the proper key
        // compare thtat carbon instacen with $Thread->updated_at

        // $key = sprintf("users.%s.visits.%s", auth()->id(), $this->id);

        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }
}
