<?php

namespace Brazzer\Admin\Auth\Database;

use Illuminate\Database\Eloquent\Model;

class SiteConfig extends Model{
    protected $fillable = [
        'name',
        'value'
    ];

    public function __construct($attributes = []){
        parent::__construct($attributes);
        $this->setConnection(config('admin.database.connection') ?: config('database.default'));
        $this->setTable(config('admin.database.site_config_table', 'site_config'));
    }
}