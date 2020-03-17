<?php

namespace Brazzer\Admin\Grid\Tools;

use Brazzer\Admin\Admin;

class ButtonURLGroup extends AbstractTool{
    protected $options;
    protected $field;
    protected $class;

    public function __construct($field = 'button_url_group', $options = [], $class = 'btn-default'){
        $this->field = $field;
        $this->options = $options;
        $this->class = $class;
    }

    public function script(){
        $url = \Request::url() . '?' . http_build_query([$this->field => '_' . $this->field . '_']);
        return <<<EOT
$('input:radio.button-group-{$this->field}').change(function () {
    var url = $(this).val();
    location.href = url;
});
EOT;
    }

    public function render(){
        Admin::script($this->script());
        $list_btn = '';
        $form_style = config('admin.form-style');
        if($this->options){
            foreach($this->options as $option => $label){
                $list_btn .= '  <label class="btn ' . $this->class . ' btn-' . $form_style . ' ' . (substr_count($option, \Request::url() . '?') ? 'active' : '') . '">
                                    <input type="radio" class="button-group-' . $this->field . '" value="' . $option . '">' . $label . '
                                </label>';
            }
        }
        return <<<EOT
<div class="btn-group btn-group-{$form_style}" data-toggle="buttons">
{$list_btn}
</div>
EOT;
    }
}
