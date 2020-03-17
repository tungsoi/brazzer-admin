<?php

namespace Brazzer\Admin\Auth\Database;

use Brazzer\Admin\Traits\AdminBuilder;
use Brazzer\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;

class Departments extends ModelApi{
    protected static $route = 'departments';
    protected static $param_only = [
        'title',
        'title_en',
        'description',
        'status'
    ];
}