<?php

namespace Brazzer\Admin\Grid\Displayers;

use Brazzer\Admin\Admin;

class Popover extends AbstractDisplayer{
    public function display($title = '', $placement = 'bottom'){
        $form_style = config('admin.form-style');
        Admin::script("$('[data-toggle=\"popover\"]').popover()");
        return <<<EOT
<button type="button"
    class="btn btn-{$form_style} btn-secondary"
    title="popover"
    data-container="body"
    data-toggle="popover"
    data-placement="$placement"
    data-content="{$this->value}"
    >
  {$title}
</button>

EOT;

    }
}