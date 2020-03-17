<?php

namespace Brazzer\Admin\Auth\Database;

use Brazzer\Admin\Traits\AdminBuilder;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

/**
 * Class Administrator.
 *
 * @property Role[] $roles
 */
class Administrator extends Model implements AuthenticatableContract{
    use Authenticatable, AdminBuilder, HasPermissions;

    protected $fillable = [
        'email',
        'password',
        'name',
        'avatar',
        'provider_id',
        'provider',
        'is_social',
        'token',
        'created_date'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public static $genders = [
        1 => 'Male',
        2 => 'Female',
    ];
    public static $contracts = [
        1 => 'Hợp đồng 12 tháng',
        2 => 'Hợp đồng 24 tháng',
        3 => 'Hợp đồng 36 tháng',
        4 => 'Hợp đồng 48 tháng',
        5 => 'Hợp đồng vô thời hạn',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = []){
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.database.users_table'));

        parent::__construct($attributes);
    }

    /**
     * Get avatar attribute.
     *
     * @param string $avatar
     *
     * @return string
     */
    public function getAvatarAttribute($avatar){
        if(url()->isValidUrl($avatar)){
            return $avatar;
        }

        $disk = config('admin.upload.disk');

        if($avatar && array_key_exists($disk, config('filesystems.disks'))){
            return Storage::disk(config('admin.upload.disk'))->url($avatar);
        }

        $default = config('admin.default_avatar') ?: '/brazzer-admin/AdminLTE/dist/img/user2-160x160.jpg';

        return admin_asset($default);
    }

    /**
     * A user has and belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany{
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id');
    }

    /**
     * A User has and belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany{
        $pivotTable = config('admin.database.user_permissions_table');

        $relatedModel = config('admin.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'permission_id');
    }

    public function getAccessToken(){
        $token = json_decode($this->token, true);
        if($token && isset($token['access_token'])){
            return $token['access_token'];
        }
        return '';
    }
}
