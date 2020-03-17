<?php

return [
    'name' => 'Brazzer Administrator',
    'logo' => '<img width="120" src="brazzer-admin/img/logo.png">',
    'logo-mini' => '<b>BB</b>',
    'bootstrap' => app_path('Admin/bootstrap.php'),
    'route' => [
        'prefix' => env('ADMIN_ROUTE_PREFIX', ''),
        'namespace' => 'App\\Admin\\Controllers',
        'middleware' => [
            'web',
            'auth'
        ],
    ],
    'directory' => app_path('Admin'),
    'title' => 'Brazzer Administrator',
    'https' => env('ADMIN_HTTPS', false),
    'auth' => [
        'controller' => Brazzer\Admin\Controllers\AuthController::class,
        'guard' => '',
        'guards' => [
            'auth' => [
                'driver' => 'session',
                'provider' => 'auth',
            ],
        ],
        'providers' => [
            'auth' => [
                'driver' => 'eloquent',
                'model' => Brazzer\Admin\Auth\Database\Administrator::class,
            ],
        ],
        // Add "remember me" to login form
        'remember' => true,
        // Redirect to the specified URI when user is not authorized.
        'redirect_to' => 'auth/login',
        // The URIs that should be excluded from authorization.
        'excepts' => [
            'auth/login',
            'auth/logout',
            '_handle_action_',
            'auth/login/brazzer',
            'auth/login/brazzer/callback',
            'auth/login/brazzer/logout',
            'auth/login/azure',
            'auth/login/azure/callback',
            'auth/login/azure/logout',
            'oauth/*',
        ],
    ],
    'form-style' => 'xs',
    //lg,sm,xs
    'login' => [
        'email' => env('AUTH_LOGIN_EMAIL', true),
        'azure' => env('AUTH_LOGIN_AZURE', false),
        'brazzer' => env('AUTH_LOGIN_BAMBOO', false),
    ],
    'upload' => [
        // Disk in `config/filesystem.php`.
        'disk' => 'admin',
        // Image and file upload path under the disk above.
        'directory' => [
            'image' => 'images',
            'file' => 'files',
        ],
    ],
    'database' => [
        // Database connection for following tables.
        'connection' => '',
        // User tables and model.
        'users_table' => 'admin_users',
        'users_model' => Brazzer\Admin\Auth\Database\Administrator::class,
        // Role table and model.
        'roles_table' => 'admin_roles',
        'roles_model' => Brazzer\Admin\Auth\Database\Role::class,
        // Permission table and model.
        'permissions_table' => 'admin_permissions',
        'permissions_model' => Brazzer\Admin\Auth\Database\Permission::class,
        // Menu table and model.
        'menu_table' => 'admin_menu',
        'menu_model' => Brazzer\Admin\Auth\Database\Menu::class,

        'titles_model' => Brazzer\Admin\Auth\Database\Titles::class,
        'departments_model' => Brazzer\Admin\Auth\Database\Departments::class,
        'contracts_model' => Brazzer\Admin\Auth\Database\Contract::class,
        'user_info_model' => Brazzer\Admin\Auth\Database\UserInfo::class,

        // Pivot table for table above.
        'operation_log_table' => 'admin_operation_log',
        'user_permissions_table' => 'admin_user_permissions',
        'role_users_table' => 'admin_role_users',
        'role_permissions_table' => 'admin_role_permissions',
        'role_menu_table' => 'admin_role_menu',
        'translations_table' => 'admin_translations',
        'notifies_table' => 'admin_notifies',
    ],
    'operation_log' => [
        'enable' => true,
        'allowed_methods' => [
            'GET',
            'HEAD',
            'POST',
            'PUT',
            'DELETE',
            'CONNECT',
            'OPTIONS',
            'TRACE',
            'PATCH'
        ],
        'except' => [
            'auth/logs*',
        ],
    ],
    'notify' => [
        'firebase' => [
            'api_key' => env('FIREBASE_API_KEY', ''),
            'auth_domain' => env('FIREBASE_AUTH_DOMAIN', ''),
            'database_url' => env('FIREBASE_DATABASE_URL', ''),
            'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', ''),
            'project_id' => env('FIREBASE_PROJECT_ID', ''),
            'table' => env('FIREBASE_TABLE', ''),
        ],
    ],
    'fields' => [
        'summernote' => [
            'lang' => 'vi-VN',
            'height' => 250,
        ]
    ],
    'translations' => [
        'delete_enabled' => true,
        'exclude_groups' => [],
        'exclude_langs' => [],
        'sort_keys ' => false,
        'trans_functions' => [
            'trans',
            'trans_choice',
            'Lang::get',
            'Lang::choice',
            'Lang::trans',
            'Lang::transChoice',
            '@lang',
            '@choice',
            '__',
            '$trans.get',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Indicates whether to check route permission.
    |--------------------------------------------------------------------------
    */
    'check_route_permission' => true,

    /*
    |--------------------------------------------------------------------------
    | Indicates whether to check menu roles.
    |--------------------------------------------------------------------------
    */
    'check_menu_roles' => true,

    /*
    |--------------------------------------------------------------------------
    | User default avatar
    |--------------------------------------------------------------------------
    |
    | Set a default avatar for newly created users.
    |
    */
    'default_avatar' => '/brazzer-admin/AdminLTE/dist/img/user2-160x160.jpg',

    /*
    |--------------------------------------------------------------------------
    | Admin map field provider
    |--------------------------------------------------------------------------
    |
    | Supported: "tencent", "google", "yandex".
    |
    */
    'map_provider' => 'google',

    /*
    |--------------------------------------------------------------------------
    | Application Skin
    |--------------------------------------------------------------------------
    |
    | This value is the skin of admin pages.
    | @see https://adminlte.io/docs/2.4/layout
    |
    | Supported:
    |    "skin-blue", "skin-blue-light", "skin-yellow", "skin-yellow-light",
    |    "skin-green", "skin-green-light", "skin-purple", "skin-purple-light",
    |    "skin-red", "skin-red-light", "skin-black", "skin-black-light".
    |
    */
    'skin' => 'skin-black-light',

    /*
    |--------------------------------------------------------------------------
    | Application layout
    |--------------------------------------------------------------------------
    |
    | This value is the layout of admin pages.
    | @see https://adminlte.io/docs/2.4/layout
    |
    | Supported: "fixed", "layout-boxed", "layout-top-nav", "sidebar-collapse",
    | "sidebar-mini".
    |
    */
    'layout' => [
        'sidebar-mini',
        'sidebar-collapse'
    ],

    /*
    |--------------------------------------------------------------------------
    | Login page background image
    |--------------------------------------------------------------------------
    |
    | This value is used to set the background image of login page.
    |
    */
    'login_background_image' => '',

    /*
    |--------------------------------------------------------------------------
    | Show version at footer
    |--------------------------------------------------------------------------
    |
    | Whether to display the version number of laravel-admin at the footer of
    | each page
    |
    */
    'show_version' => false,

    /*
    |--------------------------------------------------------------------------
    | Show environment at footer
    |--------------------------------------------------------------------------
    |
    | Whether to display the environment at the footer of each page
    |
    */
    'show_environment' => false,

    /*
    |--------------------------------------------------------------------------
    | Menu bind to permission
    |--------------------------------------------------------------------------
    |
    | whether enable menu bind to a permission
    */
    'menu_bind_permission' => false,

    /*
    |--------------------------------------------------------------------------
    | Enable default breadcrumb
    |--------------------------------------------------------------------------
    |
    | Whether enable default breadcrumb for every page content.
    */
    'enable_menu_breadcrumb' => true,
    'enable_default_breadcrumb' => false,

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable assets minify
    |--------------------------------------------------------------------------
    */
    'minify_assets' => [

        // Assets will not be minified.
        'excepts' => [

        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable sidebar menu search
    |--------------------------------------------------------------------------
    */
    'enable_menu_search' => true,

    /*
    |--------------------------------------------------------------------------
    | Alert message that will displayed on top of the page.
    |--------------------------------------------------------------------------
    */
    'top_alert' => '',

    /*
    |--------------------------------------------------------------------------
    | The global Grid action display class.
    |--------------------------------------------------------------------------
    */
    'grid_action_class' => \Brazzer\Admin\Grid\Displayers\Actions::class,

    /*
    |--------------------------------------------------------------------------
    | Extension Directory
    |--------------------------------------------------------------------------
    |
    | When you use command `php artisan admin:extend` to generate extensions,
    | the extension files will be generated in this directory.
    */
    'extension_dir' => app_path('Admin/Extensions'),

    /*
    |--------------------------------------------------------------------------
    | Settings for extensions.
    |--------------------------------------------------------------------------
    |
    | You can find all available extensions here
    | https://github.com/laravel-admin-extensions.
    |
    */
    'extensions' => [

    ],
    'model_api' => [
        'cache' => env('MODEL_API_CACHE', false),
        'store' => env('MODEL_API_CACHE_STORE', 'file'),
        'expire' => env('MODEL_API_CACHE_EXPIRE', 5),
    ]
];
