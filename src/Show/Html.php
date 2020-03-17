<?php

namespace Brazzer\Admin\Show;

class Html extends Field
{
    protected $html;
    protected $label;
    public function __construct($label= '', $html = '')
    {
        $this->label = $label;
        $this->html = $html;
        $this->setEscape(false);
    }
    public function render()
    {
        $html = $this->html;
        $label = $this->label;
        return <<<HTML
<div class="form-group form-group-xs">
    <label class="col-sm-2 control-label control-label-xs">{$label}</label>
    <div class="col-sm-8">
        <div class="box box-solid box-default no-margin box-show">
            <div class="box-body">
                {$html}
            </div>
        </div>
    </div>
</div>
HTML;
    }
}
