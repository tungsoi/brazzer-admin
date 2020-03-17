<?php

namespace Brazzer\Admin\Controllers;

use Brazzer\Admin\Facades\Admin;
use Brazzer\Admin\Form;
use Brazzer\Admin\Layout\Column;
use Brazzer\Admin\Layout\Content;
use Brazzer\Admin\Layout\Row;
use Brazzer\Admin\Tree;
use Brazzer\Admin\Tree\Filter;
use Brazzer\Admin\Widgets\Box;
use Illuminate\Routing\Controller;

class ContractController extends Controller{
    use HasResourceActions;
    protected $title = 'contract.header';

    public function index(Content $content){
        return $content->header(trans($this->title))->description(trans('admin.list'))->row(function(Row $row){
            $row->column(6, $this->treeView()->render());
            $row->column(6, function(Column $column){
                $form = new \Brazzer\Admin\Widgets\Form;
                $form->action(route('admin.contracts.store'));
                $form->hidden('parent_id', trans('level.parent_id'))->default(0);
                $form->text('title', trans('contract.title'))->rules('required');
                $form->text('title_en', trans('contract.title_en'))->rules('required');
                $form->textarea('description', trans('contract.description'));
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
        $menuModel       = config('admin.database.contracts_model');
        return $menuModel::tree(function(Tree $tree){
            $tree->disableCreate();
            $tree->filter(function (Filter $filter) {
                $filter->disableIdFilter();
                $filter->expand();
                $filter->like('title', trans('contract.title'));
                $filter->like('title_en', trans('contract.title_en'));
            });
            $tree->branch(function ($branch) {
                $payload = $branch['title'] . ' | ' . $branch['title_en'];
                return $payload;
            });
        });
    }

    public function form(){
        $menuModel       = config('admin.database.contracts_model');
        return $menuModel::form(function(Form $form){
            $form->hidden('id', 'ID');
            $form->hidden('parent_id', trans('level.parent_id'))->default(0);
            $form->text('title', trans('contract.title'))->rules('required');
            $form->text('title_en', trans('contract.title_en'))->rules('required');
            $form->textarea('description', trans('contract.description'));
            $id = $form->model()->id;
            if($id > 0){
            }else{
                $form->hidden('userid_create')->default(Admin::user()->id);
            }
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

    public function show($id){
        return redirect(route('contracts.index'));
    }
}
