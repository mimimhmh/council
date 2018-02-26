<?php

namespace App\Filters;

use Illuminate\Http\Request;

abstract class Filters
{
    protected $request, $builder;

    protected $filters = [];

    /**
     * ThreadFilters constructor.
     *
     * @param $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param $builder
     * @return mixed
     */
    public function apply($builder)
    {
        //apply filters to the builder
        //if (! $username = request('by'))
        //    return $builder;

        //if ($this->request->has('by')) {
        //    $this->by($this->request->by);
        //}

        $this->builder = $builder;

        foreach ($this->getFilters() as $filter => $value) { //['by' => 'JohnDoe']
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }

        return $this->builder;
    }

    /**
     * @return array
     */
    protected function getFilters()
    {
        return array_filter($this->request->only($this->filters));
    }
}