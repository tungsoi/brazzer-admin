<?php

namespace Brazzer\Admin\Auth\Database;


class UserInfo extends ModelApi{
    protected static $route = 'userinfo';
    protected static $param_only = [
        'user_id',
        'department_id',
        'title_id',
        'bav_id',
        'cmnd',
        'mobile',
        'gender',
        'email_personal',
        'contract_id',
        'contract_date',
        'work_date_start',
        'work_date_end',
        'note',
    ];
}