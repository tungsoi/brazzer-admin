<?php

namespace Brazzer\Admin\Controllers;

use Brazzer\Admin\Facades\Admin;
use Brazzer\Admin\Form;
use Brazzer\Admin\Layout\Column;
use Brazzer\Admin\Layout\Content;
use Brazzer\Admin\Layout\Row;
use Brazzer\Admin\Show;
use Brazzer\Admin\Tree;
use Brazzer\Admin\Tree\Filter;
use Brazzer\Admin\Widgets\Box;
use Illuminate\Routing\Controller;

class TitlesController extends Controller
{
    use ModelForm;
    protected $title = /*'Chức danh'*/
        'titles.header';

    public function index(Content $content)
    {
        return $content->header(trans($this->title))->description(trans('admin.list'))->row(function (Row $row) {
            $row->column(6, $this->treeView()->render());
            $row->column(6, function (Column $column) {
                $form = new \Brazzer\Admin\Widgets\Form;
                $form->action(route('admin.titles.store'));
                $titleModel = config('admin.database.titles_model');
                $form->select('parent_id', trans('titles.parent_id'))->options($titleModel::selectOptions());
                $form->text('title', trans('titles.title'))->rules('required');
                $form->text('title_en', trans('titles.title_en'))->rules('required');
                $form->textarea('description', trans('titles.description'));
                $departmentModel = config('admin.database.departments_model');
                $form->listbox('assign_departments', trans('titles.departments'))->options($departmentModel::selectOptions());
                $form->hidden('userid_create')->default(Admin::user()->id);
                $form->hidden('userid_update')->default(Admin::user()->id);
                $states = [
                    'on' => [
                        'value' => 1,
                        'text' => trans('admin.status_on'),
                    ],
                    'off' => [
                        'value' => 0,
                        'text' => trans('admin.status_off'),
                    ],
                ];
                $form->switch('status', trans('admin.status'))->states($states)->default(1);
                $form->hidden('_token')->default(csrf_token());

                $column->append((new Box(trans('admin.new'), $form))->style('success'));
            });
        });
    }

    public function edit($id, Content $content)
    {
        return $content->header(trans($this->title))->description(trans('admin.edit'))->body($this->form()->edit($id));
    }

    public function create(Content $content)
    {
        return $content->header(trans($this->title))->description(trans('admin.create'))->body($this->form());
    }

    protected function treeView()
    {
        $titleModel = config('admin.database.titles_model');
        return $titleModel::tree(function (Tree $tree) {
            $tree->disableCreate();
            $tree->filter(function (Filter $filter) {
                $filter->disableIdFilter();
                $filter->expand();
                $filter->like('title', trans('titles.title'));
                $filter->like('title_en', trans('titles.title_en'));
            });
            $tree->branch(function ($branch) {
                $payload = $branch['title'] . ' | ' . $branch['title_en'];
                return $payload;
            });
        });
    }

    public function form()
    {
        $titleModel = config('admin.database.titles_model');
        return $titleModel::form(function (Form $form) {
            $form->hidden('id', 'ID');
            $titleModel = config('admin.database.titles_model');
            $form->select('parent_id', trans('titles.parent_id'))->options($titleModel::selectOptions());
            $form->text('title', trans('titles.title'))->rules('required');
            $form->text('title_en', trans('titles.title_en'))->rules('required');
            $form->textarea('description', trans('titles.description'));
            $departmentModel = config('admin.database.departments_model');
            $form->listbox('assign_departments', trans('titles.departments'))->options($departmentModel::selectOptions());
            $states = [
                'on' => [
                    'value' => 1,
                    'text' => trans('admin.status_on'),
                ],
                'off' => [
                    'value' => 0,
                    'text' => trans('admin.status_off'),
                ],
            ];
            $form->switch('status', trans('admin.status'))->states($states)->default(1);
            $form->tools(function ($tools) {
                $tools->disableDelete();
                $tools->disableView();
            });
        });
    }

    public function show($id, Content $content)
    {
        return $content->header($this->title)->description(trans('admin.detail'))->body($this->detail($id));
    }

    protected function detail($id)
    {
        $titleModel = config('admin.database.titles_model');
        $show = new Show($titleModel::findOrFail($id));
        $show->title('Tên ứng dụng');
        $show->secret('Secret');
        $show->link('Link');
        $list_status = [
            0 => 'Đóng',
            1 => 'Mở',
        ];
        $show->status('Trạng thái')->as(function ($status) use ($list_status) {
            return isset($list_status[$status]) ? $list_status[$status] : '';
        });

        $show->created_at(trans('admin.created_at'));
        $show->updated_at(trans('admin.updated_at'));

        $show->panel()->tools(function ($tools) {
            $tools->disableDelete();
        });
        return $show;
    }
}
