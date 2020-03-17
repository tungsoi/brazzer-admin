<?php

namespace Brazzer\Admin\Grid\Filter\Presenter;

use Brazzer\Admin\Facades\Admin;

class Checkbox extends Radio
{
    protected function prepare()
    {
        $script = "$('.{$this->filter->getId()}').iCheck({checkboxClass:'icheckbox_minimal-blue'});";

        Admin::script($script);
    }
}
