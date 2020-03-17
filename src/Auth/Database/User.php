<?php

namespace Brazzer\Admin\Auth\Database;


class User extends ModelApi{
    protected static $route = 'user';
    protected static $param_only = [
        'id',
        'email',
        'name',
        'created_date',
    ];
}