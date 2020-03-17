<?php

namespace Brazzer\Admin\Controllers;

use Brazzer\Admin\Form;
use Brazzer\Admin\Grid;
use Brazzer\Admin\Layout\Content;
use Brazzer\Admin\Show;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class PermissionController extends Controller{
    use HasResourceActions;

    public function index(Content $content){
        return $content->header(trans('admin.permissions'))->description(trans('admin.list'))->body($this->grid()->render());
    }

    public function show($id, Content $content){
        return $content->header(trans('admin.permissions'))->description(trans('admin.detail'))->body($this->detail($id));
    }

    public function edit($id, Content $content){
        return $content->header(trans('admin.permissions'))->description(trans('admin.edit'))->body($this->form()->edit($id));
    }

    public function create(Content $content){
        return $content->header(trans('admin.permissions'))->description(trans('admin.create'))->body($this->form());
    }

    protected function grid(){
        $permissionModel = config('admin.database.permissions_model');

        $grid = new Grid(new $permissionModel());

        $grid->column('id', 'ID')->sortable();
        $grid->column('slug', trans('admin.slug'));
        $grid->column('name', trans('admin.name'));

        $grid->column('http_path', trans('admin.route'))->display(function ($path) {
            return collect(explode("\n", $path))->map(function ($path) {
                $method = $this->http_method ?: ['ANY'];
                if(Str::contains($path, ':')){
                    list($method, $path) = explode(':', $path);
                    $method = explode(',', $method);
                }
                $method = collect($method)->map(function($name){
                    return strtoupper($name);
                })->map(function($name){
                    return '<span class="label label-primary">' . $name . '</span>';
                })->implode('&nbsp;');

                if(!empty(config('admin.route.prefix'))){
                    $path = '/' . trim(config('admin.route.prefix'), '/') . $path;
                }
                return '<div style="margin-bottom: 5px;">' . $method . '<code>' . $path . '</code></div>';
            })->implode('');
        });
        $grid->filter(function($filter){
            $filter->expand();
            $filter->column(1 / 2, function($filter){
                $filter->like('name', trans('admin.name'));
            });
            $filter->column(1 / 2, function($filter){
                $filter->like('slug', trans('admin.slug'));
            });
        });
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));

        $grid->tools(function(Grid\Tools $tools){
            $tools->batch(function(Grid\Tools\BatchActions $actions){
                $actions->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        $permissionModel = config('admin.database.permissions_model');
        $show = new Show($permissionModel::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('slug', trans('admin.slug'));
        $show->field('name', trans('admin.name'));

        $show->field('http_path', trans('admin.route'))->unescape()->as(function ($path) {
            return collect(explode("\r\n", $path))->map(function ($path) {
                $method = $this->http_method ?: ['ANY'];
                if(Str::contains($path, ':')){
                    list($method, $path) = explode(':', $path);
                    $method = explode(',', $method);
                }
                $method = collect($method)->map(function($name){
                    return strtoupper($name);
                })->map(function($name){
                    return '<span class="label label-primary">' . $name . '</span>';
                })->implode('&nbsp;');

                if(!empty(config('admin.route.prefix'))){
                    $path = '/' . trim(config('admin.route.prefix'), '/') . $path;
                }
                return '<div style="margin-bottom: 5px;">' . $method . '<code>' . $path . '</code></div>';
            })->implode('');
        });

        $show->field('created_at', trans('admin.created_at'));
        $show->field('updated_at', trans('admin.updated_at'));

        return $show;
    }

    public function form(){
        $permissionModel = config('admin.database.permissions_model');
        $form = new Form(new $permissionModel);
        $form->tab(trans('permission.tab.default'), function(Form $form){
            $form->hidden('id', 'ID');
            $form->text('slug', trans('admin.slug'))->rules('required');
            $form->text('name', trans('admin.name'))->rules('required');
            $form->textarea('http_path', trans('admin.http.path'));
        })->tab(trans('permission.tab.permission'), function(Form $form){
            $form->hasMany('list_permissions', '', function(Form\NestedForm $form){
                $form->text('name', trans('admin.name'));
                $form->text('slug', trans('admin.slug'));
            });
        });
        $form->tools(function($tools){
            $tools->disableDelete();
        });
        return $form;
    }

    /**
     * Get options of HTTP methods select field.
     *
     * @return array
     */
    protected function getHttpMethodsOptions()
    {
        $model = config('admin.database.permissions_model');

        return array_combine($model::$httpMethods, $model::$httpMethods);
    }
}
