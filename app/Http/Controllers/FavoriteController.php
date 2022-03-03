<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    /**
     * FavoritesController constructor
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Reply $reply)
    {
        // Favorite::create([
        //     'user_id' => auth()->user()->id,
        //     'favorited_id' => $reply->id,
        //     'favorited_type' => get_class($reply)
        // ]);

        $reply->favorite();

        return back();
    }

    public function destroy(Reply $reply)
    {

        $reply->unfavorite();

        return back();
    }
}
