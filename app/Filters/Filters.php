<?php

namespace App\Filters;

use App\Models\User;
use Illuminate\Http\Request;

abstract class Filters
{

    protected $request, $builder;

    protected $filters = [];

    /**
     * ThreadFilters constructor
     *
     *@param Request $request
     */

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($builder)
    {
        /**
         * We apply our filters to the builder
         */

        $this->builder = $builder;

        foreach ($this->getfilters() as $filter => $value) {
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }

        return $this->builder;
    }

    public function getfilters()
    {
        // return $this->request->intersect($this->filters);

        $filters = array_intersect(array_keys($this->request->all()), $this->filters);

        return $this->request->only($filters);
    }
}
