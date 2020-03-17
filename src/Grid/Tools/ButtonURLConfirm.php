<?php

namespace Brazzer\Admin\Grid\Tools;

use Brazzer\Admin\Admin;

class ButtonURLConfirm extends AbstractTool{
    protected $options;
    protected $field;
    protected $class;
    protected $bntConfirm;

    public function __construct($field = 'button_url_confirm', $options = [], $class = 'btn-default'){
        $this->field = $field;
        $this->options = $options;
        $this->class = $class;
        $this->bntConfirm = isset($this->options['confirm']) ? $this->options['confirm'] : 'Bạn có đồng ý tiếp tục sự kiện.';
    }


    public function render(){
        Admin::script($this->script());
        $list_btn = '';
        $form_style = config('admin.form-style');
        if($this->options){
            $url = isset($this->options['url']) ? $this->options['url'] : '';
            $title = isset($this->options['title']) ? $this->options['title'] : 'title';
            $list_btn .= '<a href="#" data-href="' . $url . '" class="btn ' . $this->field . ' ' . $this->class . ' btn-' . $form_style . ' ' . (substr_count($url, \Request::url() . '?') ? 'active' : '') . '">' . $title . '</a>';
        }

        return <<<EOT
<div class="btn-group btn-group-{$form_style}" data-toggle="buttons">
{$list_btn}
</div>
EOT;
    }

    public function script(){
        $bntConfirm = $this->bntConfirm;
        $confirm = trans('admin.confirm');
        $cancel = trans('admin.cancel');
        return <<<EOT
$('.{$this->field}').on('click', function() {
    var url = $(this).data('href');
    swal({
        title: "$bntConfirm",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "$confirm",
        showLoaderOnConfirm: true,
        cancelButtonText: "$cancel",
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    method: 'post',
                    url: url,
                    success: function (data) {
                        location.href = location.href;
                    }
                });
            });
        }
    }).then(function(result) {
        var data = result.value;
        if (typeof data === 'object') {
            if (data.status) {
                swal(data.message, '', 'success');
            } else {
                swal(data.message, '', 'error');
            }
        }
    });
    return false;
});

EOT;
    }
}
