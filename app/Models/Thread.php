<?php

namespace App\Models;

use App\Events\ThreadHasNewReply;
use App\Events\ThreadReceivedNewReply;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use PDO;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;

class Thread extends Model
{
    use HasFactory, RecordsActivity, Searchable;

    protected $guarded = [];

    protected $with = ['creator', 'channel'];

    protected $appends = ['isSubscribedTo'];

    protected $casts = [
        'locked' => 'boolean'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });

        static::created(function ($thread) {
            $thread->update(['slug' => $thread->title]);
        });
    }



    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->slug}";
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

        event(new ThreadReceivedNewReply($reply));

        return $reply;
    }

    public function lock()
    {
        $this->update(['locked' => true]);
    }

    public function unlock()
    {
        $this->update(['locked' => false]);
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

    // public function visits()
    // {
    //     return new Visits($this);
    // }


    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function setSlugAttribute($value)
    {
        $slug = Str::slug($value);

        while (static::whereSlug($slug)->exists()) {
            $slug = "{$slug}-" . $this->id;
        }
        // if (static::whereSlug($slug = Str::slug($value))->exists()) {
        //     $slug = $this->incrementSlug($slug);
        // }
        $this->attributes['slug'] = $slug;
    }

    // public function incrementSlug($slug, $count = 2)
    // {
    //     $original = $slug;
    //     while (static::whereSlug($slug)->exists()) {
    //         $slug = "{$original}-" . $count++;
    //     }

    //     return $slug;

    //     // $max = static::whereTitle($this->title)->latest('id')->value('slug');

    //     // if (is_numeric($max[-1])) {
    //     //     return preg_replace_callback('/(\d+)$/', function ($matches) {
    //     //         return $matches[1] + 1;
    //     //     }, $max);
    //     // }

    //     // return "{$slug}-2";
    // }

    public function markBestReply(Reply $reply)
    {
        $this->update(['best_reply_id' => $reply->id]);
    }

    public function toSearchableArray()
    {
        return $this->toArray() + ['path' => $this->path()];
    }

    public function getBodyAttribute($body)
    {
        return \Purify::clean($body);
    }
}
