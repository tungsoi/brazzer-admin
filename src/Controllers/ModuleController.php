<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Brazzer\Admin\Controllers\ModelForm;
use Brazzer\Admin\Form;
use Brazzer\Admin\Grid;
use Brazzer\Admin\Layout\Content;
use Brazzer\Admin\Show;

class ModuleController extends Controller{
    use ModelForm;
    protected $title = 'modules.headers';

    public function index(Content $content){
        return $content->header(trans($this->title))->description(trans('admin.list'))->body($this->grid());
    }

    public function edit($id, Content $content){
        return $content->header(trans($this->title))->description(trans('admin.edit'))->body($this->form()->edit($id));
    }

    public function create(Content $content){
        return $content->header(trans($this->title))->description(trans('admin.create'))->body($this->form());
    }

    protected function grid(){
        return Module::grid(function(Grid $grid){
            $grid->title(trans('modules.title'));
            $grid->slug(trans('modules.slug'));
            $grid->created_at('Ngày tạo');
            $states = [
                'on'  => [
                    'value' => 1,
                    'text'  => trans('admin.status_on'),
                ],
                'off' => [
                    'value' => 0,
                    'text'  => trans('admin.status_off'),
                ],
            ];

            $grid->status(trans('admin.status'))->switch($states);
            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->tools(function($tools){
                $tools->disableRefreshButton();
            });
            $grid->actions(function(Grid\Displayers\Actions $actions){
                $actions->disableDelete();
            });
        });
    }

    public function form(){
        return Module::form(function(Form $form){
            $form->tab(trans('modules.tab.default'), function (Form $form){
                $form->hidden('id', 'ID');
                $form->text('title', trans('modules.title'))->rules('required');
                $form->text('slug', trans('modules.slug'))->rules('required');
                $states = [
                    'on'  => [
                        'value' => 1,
                        'text'  => trans('admin.status_on'),
                    ],
                    'off' => [
                        'value' => 0,
                        'text'  => trans('admin.status_off'),
                    ],
                ];
                $form->switch('status', trans('admin.status'))->states($states)->default(1);
            })->tab(trans('modules.tab.permission'), function (Form $form) {
                $form->hasMany('module_permissions', '', function (Form\NestedForm $form) {
                    $form->text('title', trans('modules.permission.title'));
                    $form->text('slug', trans('modules.permission.slug'));

                });
            });
            $form->tools(function($tools){
                $tools->disableDelete();
            });
        });
    }

    public function show($id, Content $content){
        return $content->header(trans($this->title))->description(trans('admin.detail'))->body($this->detail($id));
    }

    protected function detail($id){
        $show = new Show(Module::findOrFail($id));
        $show->title(trans('modules.title'));
        $show->slug(trans('modules.slug'));
        $list_status = [
            0 => trans('admin.status_off'),
            1 => trans('admin.status_on'),
        ];
        $show->status(trans('admin.status'))->as(function($status) use ($list_status){
            return isset($list_status[$status]) ? $list_status[$status] : '';
        });
        $show->panel()->tools(function($tools){
            $tools->disableDelete();
        });
        return $show;
    }
}
