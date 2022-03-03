<?php

namespace App\Filters;

use App\Models\User;
use Illuminate\Http\Request;

class ThreadFilters extends Filters
{

    protected $filters = ['by', 'popular'];

    /**
     * Filter the query by a given username
     *
     * @param string $username
     * @return mixed
     */
    protected function by($username)
    {
        $user = User::where('name', $username)->firstOrFail();
        return $this->builder->where('user_id', $user->id);
    }

    /**
     * FIlter the query according to most popular threads
     *
     * @return $this
     */

    protected function popular()
    {
        $this->builder->getQuery()->orders = [];
        return  $this->builder->orderBy('replies_count', 'desc');
    }
}
