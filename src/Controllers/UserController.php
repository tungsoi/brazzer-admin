<?php

namespace Brazzer\Admin\Controllers;

use Brazzer\Admin\Auth\Database\Role;
use Brazzer\Admin\Facades\Admin;
use Brazzer\Admin\Form;
use Brazzer\Admin\Grid;
use Brazzer\Admin\Layout\Content;
use Brazzer\Admin\Show;
use Illuminate\Routing\Controller;

class UserController extends Controller{
    use HasResourceActions;

    public function index(Content $content){
        return $content->header(trans('admin.administrator'))->description(trans('admin.list'))->body($this->grid()->render());
    }

    public function show($id, Content $content){
        return $content->header(trans('admin.administrator'))->description(trans('admin.detail'))->body($this->detail($id));
    }

    public function edit($id, Content $content){
        return $content->header(trans('admin.administrator'))->description(trans('admin.edit'))->body($this->form()->edit($id));
    }

    public function create(Content $content){
        return $content->header(trans('admin.administrator'))->description(trans('admin.create'))->body($this->form());
    }

    protected function grid(){
        $userModel = config('admin.database.users_model');
        $grid = new Grid(new $userModel);
        $grid->id('ID')->sortable();
        $grid->email(trans('admin.email'));
        $grid->name(trans('admin.name'));
        $grid->roles(trans('admin.roles'))->pluck('name')->label();
        $grid->created_at(trans('admin.created_at'));
        $grid->updated_at(trans('admin.updated_at'));
        $grid->filter(function($filter){
            $filter->expand();
            $filter->column(1 / 2, function($filter){
                $filter->like('name', trans('admin.name'));
            });
            $filter->column(1 / 2, function($filter){
                $filter->like('email', trans('admin.email'));
                $filter->equal('roles.id', trans('admin.role'))->select(Role::all()->pluck('name', 'id'));
            });
        });
        $grid->actions(function(Grid\Displayers\Actions $actions){
            if($actions->getKey() == 1){
                $actions->disableDelete();
            }
            if(Admin::user()->isAdministrator()){
                $url_loginas = route('admin.loginas', ['id' => $this->row->id]);
                $actions->prepend('<a href="' . $url_loginas . '" data-toggle="tooltip" data-original-title="Login as ' . $this->row->name . '"><i class="fa fa-sign-out loginas"></i></a>');
            }
        });
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
    protected function detail($id){
        $userModel = config('admin.database.users_model');
        $show = new Show($userModel::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('name', trans('admin.name'));
        $show->field('roles', trans('admin.roles'))->as(function($roles){
            return $roles->pluck('name');
        })->label();
        $show->field('permissions', trans('admin.permissions'))->as(function($permission){
            return $permission->pluck('name');
        })->label();
        $show->field('created_at', trans('admin.created_at'));
        $show->field('updated_at', trans('admin.updated_at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form(){
        $userModel = config('admin.database.users_model');
        $form = new Form(new $userModel);
        if(\request()->route()->getActionMethod() == 'edit'){
            $form->display('email', trans('admin.email'));
        }else{
            $userTable = config('admin.database.users_table');
            $userNameRules = "required|unique:{$userTable}";
            $form->text('email', trans('user.email'))->rules($userNameRules);
        }
        $form->text('name', trans('user.name'))->rules('required');
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');
        $form->multipleSelect('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'));
        $form->multipleSelect('permissions', trans('admin.permissions'))->options($permissionModel::all()->pluck('name', 'id'));
        if(config('admin.login.email')){
            //if(\request()->route()->getActionMethod() == 'edit' && (isset($form->model_data) && !substr_count($form->model_data->email, '@brazzerairways.com'))){
                $form->password('password', trans('admin.password'))->rules('required|confirmed')->default(function($form){
                    return $form->model()->password;
                });
                $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')->default(function($form){
                    return $form->model()->password;
                });
                $form->ignore(['password_confirmation']);
            //}
        }
        /*$form->tab('user.tab_infomation', function(Form $form) use ($userModel){
            $form->text('info.bav_id', trans('user.bav_id'));
            $form->text('name', trans('user.name'))->rules('required');
            if(\request()->route()->getActionMethod() == 'edit'){
                $form->display('email', trans('admin.email'));
            }else{
                $userTable = config('admin.database.users_table');
                $userNameRules = "required|unique:{$userTable}";
                $form->text('email', trans('user.email'))->rules($userNameRules);
            }
            $form->select('info.gender', trans('user.gender'))->options($userModel::$genders);
            //$form->image('avatar', trans('user.avatar'));
            $titleModel = config('admin.database.titles_model');
            $departmentModel = config('admin.database.departments_model');
            $form->select('info.title_id', trans('user.titles'))->options($titleModel::selectOptions());
            $form->select('info.department_id', trans('user.department'))->options($departmentModel::selectOptions());
            $contractModel = config('admin.database.contracts_model');
            $form->text('info.email_personal', trans('user.email_personal'));
            $form->select('info.contract_id', trans('user.contract_id'))->options($contractModel::selectOptions());
            $form->date('info.work_date_start', trans('user.work_date_start'));
            $form->date('info.contract_date', trans('user.contract_date'));
            $form->date('info.work_date_end', trans('user.work_date_end'));
            $form->textarea('info.note', trans('user.note'));
        });
        $form->tab('user.tab_setting', function(Form $form){
            $form->column(1 / 2, function(Form $form){
                $permissionModel = config('admin.database.permissions_model');
                $roleModel = config('admin.database.roles_model');
                $form->multipleSelect('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'));
                $form->multipleSelect('permissions', trans('admin.permissions'))->options($permissionModel::all()->pluck('name', 'id'));
            });
            $form->column(1 / 2, function(Form $form){
                if(config('admin.login.email')){
                    if(\request()->route()->getActionMethod() == 'edit' && (isset($form->model_data) && !substr_count($form->model_data->email, '@brazzerairways.com'))){
                        $form->password('password', trans('admin.password'))->rules('required|confirmed')->default(function($form){
                            return $form->model()->password;
                        });
                        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')->default(function($form){
                            return $form->model()->password;
                        });
                        $form->ignore(['password_confirmation']);
                    }
                }
            });
        });*/

        $form->hidden('id', 'ID');
        $form->saving(function(Form $form){
            if(!substr_count($form->model()->email, '@outlook.com')) {
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }
            }
        });
        $form->saved(function(Form $form){
            if(substr_count($form->model()->email, '@outlook.com')){
                $form->model()->password = null;
                $form->model()->save();
            }
        });
        return $form;
    }

    public function search(){
        $q = request()->get('q');
        if($q){
            $userModel = config('admin.database.users_model');
            return $userModel::where('name', 'like', "%$q%")->paginate(null, [
                'id',
                'name as text'
            ]);
        }
        return [];
    }
}

