<?php

namespace Brazzer\Admin\Grid\Tools;

use Brazzer\Admin\Grid;

class CreateButton extends AbstractTool{
    /**
     * @var Grid
     */
    protected $grid;

    /**
     * Create a new CreateButton instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid){
        $this->grid = $grid;
    }

    /**
     * Render CreateButton.
     *
     * @return string
     */
    public function render(){
        if(!$this->grid->showCreateBtn()){
            return '';
        }
        $new = trans('admin.new');
        $form_style = config('admin.form-style');
        return <<<EOT
<div class="btn-group btn-group-{$form_style} pull-right" style="margin-right: 10px">
    <a href="{$this->grid->getCreateUrl()}" class="btn btn-{$form_style} btn-success" title="{$new}">
        <i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;{$new}</span>
    </a>
</div>

EOT;
    }
}
