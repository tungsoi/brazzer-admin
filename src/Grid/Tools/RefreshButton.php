<?php

namespace Brazzer\Admin\Grid\Tools;

use Brazzer\Admin\Admin;

class RefreshButton extends AbstractTool{
    /**
     * Script for this tool.
     *
     * @return string
     */
    protected function script(){
        $message = trans('admin.refresh_succeeded');
        return <<<EOT
$('.grid-refresh').on('click', function() {
    $.pjax.reload('#pjax-container');
    toastr.success('{$message}');
});
EOT;
    }

    /**
     * Render refresh button of grid.
     *
     * @return string
     */
    public function render(){
        Admin::script($this->script());
        $form_style = config('admin.form-style');
        $refresh = trans('admin.refresh');
        return <<<EOT
<a class="btn btn-{$form_style} btn-primary grid-refresh" title="$refresh"><i class="fa fa-refresh"></i><span class="hidden-xs"> $refresh</span></a>
EOT;
    }
}
