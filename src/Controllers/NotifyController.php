<?php

namespace Brazzer\Admin\Controllers;

use App\Models\GiftVoucher\Product;
use Brazzer\Admin\Auth\Database\Notify;
use Brazzer\Admin\Facades\Admin;
use Brazzer\Admin\Form;
use Brazzer\Admin\Grid;
use Brazzer\Admin\Layout\Content;
use Illuminate\Routing\Controller;

class NotifyController extends Controller
{
    use ModelForm;
    protected $title = 'notify.header';

    public function index(Content $content)
    {
        return $content->header(trans($this->title))->description(trans('admin.list'))->body($this->grid());
    }

    protected function grid()
    {
        return Notify::grid(function (Grid $grid) {
            $grid->model()->where('user_id', Admin::user()->id);
            $grid->model()->orderBy('id', 'DESC');
            //$grid->type(trans('notify.type'))->using(['']);
            $grid->icon(trans('notify.icon'))->display(function ($icon) {
                return '<i class="fa fa-' . ($icon != '' ? $icon : 'circle-o') . '"></i>';
            });
            $grid->messenger(trans('notify.messenger'));
            $grid->link(trans('notify.link'))->display(function ($link) {
                if ($link != '') {
                    return '<a href="' . $link . '" target="_blank">' . $link . '</a>';
                }
            });
            $states = [
                'on' => [
                    'value' => 1,
                    'text' => trans('notify.read'),
                ],
                'off' => [
                    'value' => 0,
                    'text' => trans('notify.not_read'),
                ],
            ];
            $grid->is_read(trans('notify.type'))->switch($states);
            /*$grid->is_read(trans('notify.type'))->display(function ($is_read) {
                return $is_read == 1 ? trans('notify.read') : trans('notify.not_read');
            });*/
            $grid->created_at(trans('admin.created_at'));
            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->disableActions();
            $grid->tools(function ($tools) {
                $tools->disableRefreshButton();
            });
        });
    }

    public function form()
    {
        return Notify::form(function (Form $form) {
            $form->hidden('id', 'ID');
            $states = [
                'on' => [
                    'value' => 1,
                    'text' => trans('notify.read'),
                ],
                'off' => [
                    'value' => 0,
                    'text' => trans('notify.not_read'),
                ],
            ];
            $form->switch('is_read', trans('admin.status'))->states($states)->default(1);
            $form->tools(function ($tools) {
                $tools->disableDelete();
            });
        });
    }
}
