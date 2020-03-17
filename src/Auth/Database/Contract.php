<?php

namespace Brazzer\Admin\Auth\Database;


class Contract extends ModelApi{
    protected static $route = 'contracts';
    protected static $param_only = [
        'title',
        'title_en',
        'description',
        'status'
    ];
}