<?php

namespace Brazzer\Admin\Auth\Database;

use Brazzer\Admin\Facades\Admin;
use Brazzer\Admin\Traits\AdminBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mpociot\Firebase\SyncsWithFirebase;

class Notify extends Model
{
    use AdminBuilder, SyncsWithFirebase;
    protected $fillable = [
        'user_id',
        'type',/* 1:text; 2:download; 3:link;*/
        'icon',
        'is_read',
        'messenger',
        'link',
        'target',
    ];

    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');
        $this->setConnection($connection);
        $this->setTable(config('admin.database.notifies_table'));
        parent::__construct($attributes);
    }
    public static function last_id()
    {
        $noti = Notify::where('user_id', Admin::user()->id)->orderBy('id', 'DESC')->first();
        return $noti ? $noti->id : 0;
    }
}
