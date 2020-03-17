<?php

namespace Brazzer\Admin\Tree\Concerns;

use Closure;
use Brazzer\Admin\Tree\Filter;
use Illuminate\Support\Collection;

trait HasFilter
{
    /**
     * The grid Filter.
     *
     * @var \Brazzer\Admin\Grid\Filter
     */
    protected $filter;

    /**
     * Setup grid filter.
     *
     * @return void
     */
    protected function setupFilter()
    {
        $this->filter = new Filter($this->model());
    }

    /**
     * Disable grid filter.
     *
     * @return $this
     */
    public function disableFilter(bool $disable = true)
    {
        $this->tools->disableFilterButton($disable);

        return $this->option('show_filter', !$disable);
    }

    /**
     * Get filter of Grid.
     *
     * @return Filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Process the grid filter.
     *
     * @param bool $toArray
     *
     * @return array|Collection|mixed
     */
    public function processFilter($toArray = true)
    {
        /*if ($this->builder) {
            call_user_func($this->builder, $this);
        }*/

        return $this->filter->execute($toArray);
    }

    /**
     * Set the grid filter.
     *
     * @param Closure $callback
     */
    public function filter(Closure $callback)
    {
        call_user_func($callback, $this->filter);
    }

    /**
     * Render the grid filter.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function renderFilter()
    {

        return $this->filter->render();
    }

    /**
     * Expand filter.
     *
     * @return $this
     */
    public function expandFilter()
    {
        $this->filter->expand();

        return $this;
    }
}
