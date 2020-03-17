<?php

namespace Brazzer\Admin;

use Brazzer\Admin\Auth\Database\Menu;
use Brazzer\Admin\Controllers\AuthController;
use Brazzer\Admin\Layout\Content;
use Brazzer\Admin\Traits\HasAssets;
use Brazzer\Admin\Widgets\Navbar;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

/**
 * Class Admin.
 */
class Admin
{
    use HasAssets;

    /**
     * The Laravel admin version.
     *
     * @var string
     */
    const VERSION = '1.6.15';

    /**
     * @var Navbar
     */
    protected $navbar;

    /**
     * @var array
     */
    protected $menu = [];

    /**
     * @var string
     */
    public static $metaTitle;

    /**
     * @var string
     */
    public static $favicon;

    /**
     * @var array
     */
    public static $extensions = [];

    /**
     * @var []Closure
     */
    protected static $bootingCallbacks = [];

    /**
     * @var []Closure
     */
    protected static $bootedCallbacks = [];

    /**
     * Returns the long version of Laravel-admin.
     *
     * @return string The long application version
     */
    public static function getLongVersion()
    {
        return sprintf('Laravel-admin <comment>version</comment> <info>%s</info>', self::VERSION);
    }

    /**
     * @param $model
     * @param Closure $callable
     *
     * @return \Brazzer\Admin\Grid
     *
     * @deprecated since v1.6.1
     */
    public function grid($model, Closure $callable)
    {
        return new Grid($this->getModel($model), $callable);
    }

    /**
     * @param $model
     * @param Closure $callable
     *
     * @return \Brazzer\Admin\Form
     *
     * @deprecated since v1.6.1
     */
    public function form($model, Closure $callable)
    {
        return new Form($this->getModel($model), $callable);
    }

    /**
     * Build a tree.
     *
     * @param $model
     * @param Closure|null $callable
     *
     * @return \Brazzer\Admin\Tree
     */
    public function tree($model, Closure $callable = null)
    {
        return new Tree($this->getModel($model), $callable);
    }

    /**
     * Build show page.
     *
     * @param $model
     * @param mixed $callable
     *
     * @return Show
     *
     * @deprecated since v1.6.1
     */
    public function show($model, $callable = null)
    {
        return new Show($this->getModel($model), $callable);
    }

    /**
     * @param Closure $callable
     *
     * @return \Brazzer\Admin\Layout\Content
     *
     * @deprecated since v1.6.1
     */
    public function content(Closure $callable = null)
    {
        return new Content($callable);
    }

    /**
     * @param $model
     *
     * @return mixed
     */
    public function getModel($model)
    {
        if ($model instanceof Model) {
            return $model;
        }

        if (is_string($model) && class_exists($model)) {
            return $this->getModel(new $model());
        }

        throw new InvalidArgumentException("$model is not a valid model");
    }

    /**
     * Left sider-bar menu.
     *
     * @return array
     */
    public function menu()
    {
        if (!empty($this->menu)) {
            return $this->menu;
        }

        $menuClass = config('admin.database.menu_model');

        /** @var Menu $menuModel */
        $menuModel = new $menuClass();

        return $this->menu = $menuModel->toTree();
    }

    public function menuData($type = [
        1,
        2,
        3
    ], $result = [], $data = [])
    {
        if (empty($data)) {
            $data = $this->menu();
        }
        foreach ($data as $item) {
            if (in_array($item['type_id'], $type)) {
                $result[] = $item;
                if (isset($item['children'])) {
                    $this->menuData($type, $result, $item['children']);
                }
            }
        }
        return $result;
    }

    public function menuBreadCrumb($result = [], $data = [], $uri = '')
    {
        if ($uri == '') {
            $uri = request()->path();
        }
        if (empty($data)) {
            $data = Admin::menu();
        }
        if (substr_count($uri, '/create')) {
            $uri = str_replace('/create', '', $uri);
        } elseif (substr_count($uri, '/edit')) {
            $uri = preg_replace('/\/(\d+)\/edit/i', '', $uri);
        } elseif (preg_match('/\/(\d+)/i', $uri)) {
            $uri = preg_replace('/\/(\d+)/i', '', $uri);
        }
        foreach ($data as $item) {
            if (Admin::user()->visible($item['roles']) && (empty($item['permission']) ?: Admin::user()->can($item['permission']))) {
                if (isset($item['children'])) {
                    $result = $this->menuBreadCrumb($result, $item['children']);
                }
                if ($item['uri'] == $uri) {
                    $item['list_data'] = $data;
                    $result[] = $item;
                    if ($item['parent_id'] > 0) {
                        $result = $this->menuBreadCrumbParent($result, $item['parent_id']);
                    }
                }
            }
        }
        rsort($result);
        return $result;
    }

    protected function menuBreadCrumbParent($result = [], $parent_id = 0)
    {
        foreach (Admin::menu() as $item) {
            if ($item['id'] == $parent_id) {
                $item['list_data'] = $this->menuBreadCrumbParentData(Admin::menu(), $item['id']);
                $result[] = $item;
                if ($item['parent_id'] > 0) {
                    $result = $this->menuBreadCrumbParent($result, $item['parent_id']);
                }
            }
        }
        return $result;
    }

    protected function menuBreadCrumbParentData($data = [], $id = 0)
    {
        $result = [];
        foreach ($data as $item) {
            if ($item['id'] == $id) {
                $result = $data;
            } else {
                if (isset($item['children'])) {
                    $result = $this->menuBreadCrumbParentData($item['children'], $id);
                }
            }
        }
        return $result;
    }

    /**
     * @param array $menu
     *
     * @return array
     */
    public function menuLinks($menu = [])
    {
        if (empty($menu)) {
            $menu = $this->menu();
        }

        $links = [];

        foreach ($menu as $item) {
            if (!empty($item['children'])) {
                $links = array_merge($links, $this->menuLinks($item['children']));
            } else {
                $links[] = Arr::only($item, [
                    'title',
                    'uri',
                    'icon'
                ]);
            }
        }

        return $links;
    }

    /**
     * Set admin title.
     *
     * @param string $title
     *
     * @return void
     */
    public static function setTitle($title)
    {
        self::$metaTitle = $title;
    }

    /**
     * Get admin title.
     *
     * @return string
     */
    public function title()
    {
        return self::$metaTitle ? self::$metaTitle : config('admin.title');
    }

    /**
     * @param null|string $favicon
     *
     * @return string|void
     */
    public function favicon($favicon = null)
    {
        if (is_null($favicon)) {
            return static::$favicon;
        }

        static::$favicon = $favicon;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        return $this->guard()->user();
    }

    /**
     * Attempt to get the guard from the local cache.
     *
     * @return \Illuminate\Contracts\Auth\Guard|\Illuminate\Contracts\Auth\StatefulGuard
     */
    public function guard()
    {
        $guard = config('admin.auth.guard') ?: '';

        return Auth::guard($guard);
    }

    /**
     * Set navbar.
     *
     * @param Closure|null $builder
     *
     * @return Navbar
     */
    public function navbar(Closure $builder = null)
    {
        if (is_null($builder)) {
            return $this->getNavbar();
        }

        call_user_func($builder, $this->getNavbar());
    }

    /**
     * Get navbar object.
     *
     * @return \Brazzer\Admin\Widgets\Navbar
     */
    public function getNavbar()
    {
        if (is_null($this->navbar)) {
            $this->navbar = new Navbar();
        }

        return $this->navbar;
    }

    /**
     * Register the laravel-admin builtin routes.
     *
     * @return void
     *
     * @deprecated Use Admin::routes() instead();
     */
    public function registerAuthRoutes()
    {
        $this->routes();
    }

    /**
     * Register the laravel-admin builtin routes.
     *
     * @return void
     */
    public function routes()
    {
        $attributes = [
            'prefix' => config('admin.route.prefix'),
            'middleware' => config('admin.route.middleware'),
        ];

        app('router')->group($attributes, function ($router) {

            /* @var \Illuminate\Support\Facades\Route $router */
            $router->namespace('\Brazzer\Admin\Controllers')->group(function ($router) {

                /* @var \Illuminate\Routing\Router $router */
                $router->get('auth/users/search', 'UserController@search')->name('admin.users.search');

                $router->resource('departments', 'DepartmentsController')->names('admin.departments');
                $router->resource('titles', 'TitlesController')->names('admin.titles');
                $router->resource('contracts', 'ContractController')->names('admin.contracts');
                $router->resource('notify', 'NotifyController')->names('admin.notifies');

                $router->resource('auth/users', 'UserController')->names('admin.auth.users');
                $router->resource('auth/roles', 'RoleController')->names('admin.auth.roles');
                $router->resource('auth/permissions', 'PermissionController')->names('admin.auth.permissions');
                $router->resource('auth/menu', 'MenuController', ['except' => ['create']])->names('admin.auth.menu');
                $router->resource('auth/logs', 'LogController', [
                    'only' => [
                        'index',
                        'destroy'
                    ]
                ])->names('admin.auth.logs');

                $router->post('_handle_form_', 'HandleController@handleForm')->name('admin.handle-form');
                $router->post('_handle_action_', 'HandleController@handleAction')->name('admin.handle-action');
            });

            $authController = config('admin.auth.controller', AuthController::class);

            /* @var \Illuminate\Routing\Router $router */
            $router->get('auth/login', $authController . '@getLogin')->name('admin.login');
            $router->post('auth/login', $authController . '@postLogin')->name('admin.login');
            $router->get('auth/logout', $authController . '@getLogout')->name('admin.logout');
            $router->get('auth/loginas', $authController . '@loginas')->name('admin.loginas');
            $router->get('auth/setting', $authController . '@getSetting')->name('admin.setting');
            $router->put('auth/setting', $authController . '@putSetting');

            $router->get('auth/login/brazzer', '\Brazzer\Admin\Controllers\Auth\BrazzerController@login')->name('admin.login.brazzer');
            $router->get('auth/login/brazzer/callback', '\Brazzer\Admin\Controllers\Auth\BrazzerController@loginCallback')->name('admin.login.brazzer.callback');
            $router->get('auth/login/brazzer/logout', '\Brazzer\Admin\Controllers\Auth\BrazzerController@logout')->name('admin.logout.brazzer');

            $router->get('auth/login/azure', '\Brazzer\Admin\Controllers\Auth\AzureController@login')->name('admin.login.azure');
            $router->get('auth/login/azure/callback', '\Brazzer\Admin\Controllers\Auth\AzureController@loginCallback')->name('admin.login.azure.callback');
            $router->get('auth/login/azure/logout', '\Brazzer\Admin\Controllers\Auth\AzureController@logout')->name('admin.logout.azure');

            $router->get('logviewer', '\Brazzer\Admin\Controllers\LogViewerController@index')->name('admin.logviewer.index');
            $router->get('logviewer/{file}', '\Brazzer\Admin\Controllers\LogViewerController@index')->name('admin.logviewer.file');
            $router->get('logviewer/{file}/tail', '\Brazzer\Admin\Controllers\LogViewerController@tail')->name('admin.logviewer.tail');


            $router->namespace('\Brazzer\Admin\Controllers')->group(function ($router) {
                $router->get('siteconfig',  'SiteConfigController@index')->name('admin.siteconfig.index');
                $router->post('siteconfig', 'SiteConfigController@update')->name('admin.siteconfig.index');
                $router->get('translations/view/{groupKey?}', 'TranslationController@getView')->where('groupKey', '.*')->name('admin.translation.view');
                $router->get('translations/{groupKey?}', 'TranslationController@getIndex')->where('groupKey', '.*')->name('admin.translation.index');
                $router->post('translations/add/{groupKey}', 'TranslationController@postAdd')->where('groupKey', '.*')->name('admin.translation.add');
                $router->any('translations/edit/{groupKey}', 'TranslationController@postEdit')->where('groupKey', '.*')->name('admin.translation.edit');
                $router->post('translations/groups/add', 'TranslationController@postAddGroup')->name('admin.translation.group.add');
                $router->post('translations/delete/{groupKey}/{translationKey}', 'TranslationController@postDelete')->where('groupKey', '.*')->name('admin.translation.delete');
                $router->post('translations/import', 'TranslationController@postImport')->name('admin.translation.import');
                $router->post('translations/find', 'TranslationController@postFind')->name('admin.translation.find');
                $router->post('translations/locales/add', 'TranslationController@postAddLocale')->name('admin.translation.locales.add');
                $router->post('translations/locales/remove', 'TranslationController@postRemoveLocale')->name('admin.translation.locales.delete');
                $router->post('translations/publish/{groupKey}', 'TranslationController@postPublish')->where('groupKey', '.*')->name('admin.translation.publish');
            });
        });
    }

    /**
     * Extend a extension.
     *
     * @param string $name
     * @param string $class
     *
     * @return void
     */
    public static function extend($name, $class)
    {
        static::$extensions[$name] = $class;
    }

    /**
     * @param callable $callback
     */
    public static function booting(callable $callback)
    {
        static::$bootingCallbacks[] = $callback;
    }

    /**
     * @param callable $callback
     */
    public static function booted(callable $callback)
    {
        static::$bootedCallbacks[] = $callback;
    }

    /**
     * Bootstrap the admin application.
     */
    public function bootstrap()
    {
        $this->fireBootingCallbacks();

        require config('admin.bootstrap', admin_path('bootstrap.php'));

        $this->addAdminAssets();

        $this->fireBootedCallbacks();
    }

    /**
     * Add JS & CSS assets to pages.
     */
    protected function addAdminAssets()
    {
        $assets = Form::collectFieldAssets();

        self::css($assets['css']);
        self::js($assets['js']);
    }

    /**
     * Call the booting callbacks for the admin application.
     */
    protected function fireBootingCallbacks()
    {
        foreach (static::$bootingCallbacks as $callable) {
            call_user_func($callable);
        }
    }

    /**
     * Call the booted callbacks for the admin application.
     */
    protected function fireBootedCallbacks()
    {
        foreach (static::$bootedCallbacks as $callable) {
            call_user_func($callable);
        }
    }

    /*
     * Disable Pjax for current Request
     *
     * @return void
     */
    public function disablePjax()
    {
        if (request()->pjax()) {
            request()->headers->set('X-PJAX', false);
        }
    }
}
