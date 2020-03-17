<?php

namespace Brazzer\Admin\Controllers;

use Brazzer\Admin\Facades\Admin;
use Brazzer\Admin\Form;
use Brazzer\Admin\Grid;
use Brazzer\Admin\Layout\Column;
use Brazzer\Admin\Layout\Content;
use Brazzer\Admin\Layout\Row;
use Brazzer\Admin\Tree\Filter;
use Brazzer\Admin\Widgets\Box;
use Illuminate\Routing\Controller;
use Brazzer\Admin\Tree;
use Illuminate\Support\Facades\Request;

class DepartmentsController extends Controller{
    use ModelForm;
    protected $title = 'departments.header';
    protected $list_type = [
        1 => 'Khối',
        2 => 'Ban',
        3 => 'Phòng'
    ];

    public function index(Content $content){
        return $content->header(trans($this->title))->description(trans('admin.list'))->row(function(Row $row){
            $row->column(6, $this->treeView()->render());
            $row->column(6, function(Column $column){
                $list_type = $this->list_type;
                $form = new \Brazzer\Admin\Widgets\Form;
                $form->action(route('admin.departments.store'));
                $departmentModel = config('admin.database.departments_model');
                $form->select('parent_id', trans('departments.parent_id'))->options($departmentModel::selectOptions());
                $form->text('title', trans('departments.title'))->rules('required');
                $form->text('title_en', trans('departments.title_en'))->rules('required');
                $form->select('type_id', trans('departments.type_id'))->options($list_type);
                $form->textarea('description', trans('departments.description'));
                $titleModel = config('admin.database.titles_model');
                $form->listbox('assign_titles', trans('departments.titles'))->options($titleModel::selectOptions());
                $form->hidden('userid_create')->default(Admin::user()->id);
                $form->hidden('userid_update')->default(Admin::user()->id);
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
                $form->hidden('_token')->default(csrf_token());

                $column->append((new Box(trans('admin.new'), $form))->style('success'));
            });
        });
    }

    public function edit($id, Content $content){
        return $content->header(trans($this->title))->description(trans('admin.edit'))->body($this->form()->edit($id));
    }

    public function create(Content $content){
        return $content->header(trans($this->title))->description(trans('admin.create'))->body($this->form());
    }
    protected function treeView(){
        $departmentModel = config('admin.database.departments_model');
        return $departmentModel::tree(function(Tree $tree){
            $tree->disableCreate();
            $tree->filter(function (Filter $filter) {
                $filter->disableIdFilter();
                $filter->expand();
                $filter->like('title', trans('departments.title'));
                $filter->like('title_en', trans('departments.title_en'));
            });
            $tree->branch(function ($branch) {
                $payload = $branch['title'] . ' | ' . $branch['title_en'];
                return $payload;
            });
        });
    }
    public function form(){
        $departmentModel = config('admin.database.departments_model');
        return $departmentModel::form(function(Form $form){
            $list_type = $this->list_type;
            $form->hidden('id', 'ID');
            $departmentModel = config('admin.database.departments_model');
            $form->select('parent_id', trans('departments.parent_id'))->options($departmentModel::selectOptions());
            $form->text('title', trans('departments.title'))->rules('required');
            $form->text('title_en', trans('departments.title_en'))->rules('required');
            $form->select('type_id', trans('departments.type_id'))->options($list_type);
            $form->textarea('description', trans('departments.description'));
            $titleModel = config('admin.database.titles_model');
            $form->listbox('assign_titles', trans('departments.titles'))->options($titleModel::selectOptions());
            $form->hidden('userid_update')->default(Admin::user()->id);
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
            $form->tools(function($tools){
                $tools->disableDelete();
                $tools->disableView();
            });
        });
    }


}
