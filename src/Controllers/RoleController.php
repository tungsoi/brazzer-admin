<?php

namespace Brazzer\Admin\Controllers;

use Brazzer\Admin\Auth\Database\Permission;
use Brazzer\Admin\Facades\Admin;
use Brazzer\Admin\Form;
use Brazzer\Admin\Grid;
use Brazzer\Admin\Layout\Content;
use Brazzer\Admin\Show;
use Illuminate\Routing\Controller;

class RoleController extends Controller{
    use HasResourceActions;

    public function index(Content $content){
        return $content->header(trans('admin.roles'))->description(trans('admin.list'))->body($this->grid()->render());
    }

    public function show($id, Content $content){
        return $content->header(trans('admin.roles'))->description(trans('admin.detail'))->body($this->detail($id));
    }

    public function edit($id, Content $content){
        return $content->header(trans('admin.roles'))->description(trans('admin.edit'))->body($this->form($id)->edit($id));
    }

    public function create(Content $content){
        return $content->header(trans('admin.roles'))->description(trans('admin.create'))->body($this->form());
    }

    protected function grid(){
        $roleModel = config('admin.database.roles_model');
        $grid = new Grid(new $roleModel);
        $grid->id('ID')->sortable();
        $grid->slug(trans('admin.slug'));
        $grid->name(trans('admin.name'));
        $grid->permissions(trans('admin.permission'))->where('permission_id', 0)->pluck('name')->label();
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));
        $grid->filter(function($filter){
            $filter->expand();
            $filter->column(1 / 2, function($filter){
                $filter->like('name', trans('admin.name'));
            });
            $filter->column(1 / 2, function($filter){
                $filter->like('slug', trans('admin.slug'));
                $filter->equal('permissions.id', trans('admin.permission'))->select(Permission::where('permission_id', 0)->get()->pluck('name', 'id'));
            });
        });
        $grid->actions(function(Grid\Displayers\Actions $actions){
            if($actions->row->slug == 'administrator'){
                $actions->disableDelete();
            }
        });
        $grid->tools(function(Grid\Tools $tools){
            $tools->batch(function(Grid\Tools\BatchActions $actions){
                $actions->disableDelete();
            });
        });
        return $grid;
    }

    protected function detail($id){
        $roleModel = config('admin.database.roles_model');
        $show = new Show($roleModel::findOrFail($id));
        $show->id('ID');
        $show->slug(trans('admin.slug'));
        $show->name(trans('admin.name'));
        $show->permissions(trans('admin.permissions'))->as(function($permission){
            return $permission->pluck('name');
        })->label();
        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));
        return $show;
    }

    public function form($id = 0){
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');
        if($id == 0){
            $id = request('id', 0);
        }
        $form = new Form(new $roleModel);
        $form->hidden('id', 'ID');
        $form->text('slug', trans('admin.slug'))->rules('required');
        $form->text('name', trans('admin.name'))->rules('required');
        $list_module = $permissionModel::where('permission_id', 0)->get();
        $role = $roleModel::find($id);
        $permissions = [];
        if($role && isset($role->permissions)){
            $permissions = $role->permissions()->get()->pluck('id')->toArray();
        }
        $html = '';
        Admin::script("$('.module-permissions').iCheck({checkboxClass:'icheckbox_minimal-blue'});");
        foreach($list_module as $module){
            $checked = in_array($module->id, $permissions) ? 'checked' : '';
            $class_key = str_replace([
                '.',
                '*'
            ], [
                '-',
                '-'
            ], $module->slug);
            $html .= '<fieldset>';
            $html .= '    <figure>
                                <label>
                                    <input type="checkbox" ' . $checked . ' value="' . $module->id . '" name="permissions[]" class="module-permissions ' . $class_key . '_all"> ' . $module->name . '
                                </label>
                            </figure>';
            $list_module_permissions = $module->list_permissions()->get();
            foreach($list_module_permissions as $module_permissions){
                $checked = in_array($module_permissions->id, $permissions) ? 'checked' : '';
                $html .= '      <div class="col-md-2">
                                    <label>
                                        <input type="checkbox" ' . $checked . ' value="' . $module_permissions->id . '" name="permissions[]" class="module-permissions ' . $class_key . '" value="' . $module_permissions->slug . '"> ' . $module_permissions->name . '
                                    </label>
                                </div>';
            }
            $html .= '</fieldset>';
            $script = "$('." . $class_key . "_all').iCheck({checkboxClass:'icheckbox_minimal-blue'}).on('ifChecked', function () {
                            $('." . $class_key . "').iCheck('check');
                        }).on('ifUnchecked', function () {
                            $('." . $class_key . "').iCheck('uncheck');
                        });";
            Admin::script($script);
        }
        Admin::script("$('.module-permissions').iCheck({checkboxClass:'icheckbox_minimal-blue'});");
        $form->html($html, trans('admin.module'));
        $form->multipleSelect('permissions', trans('admin.permissions'))->setGroupClass('hidden');
        return $form;
    }
}
