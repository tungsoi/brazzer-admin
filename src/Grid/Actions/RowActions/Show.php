<?php

namespace Brazzer\Admin\Grid\Actions\RowActions;

use Brazzer\Admin\Actions\RowAction;

class Show extends RowAction
{
    /**
     * @return array|null|string
     */
    public function name()
    {
        return __('admin.show');
    }

    /**
     * @return string
     */
    public function href()
    {
        return "{$this->getResource()}/{$this->getKey()}";
    }

    public function render()
    {
        $title = $this->asColumn ? $this->display($this->row($this->column->getName())) : $this->name();
        return "<a href='{$this->href()}' data-toggle='tooltip' title='{$title}'><i class='fa fa-eye'></i></a>";
    }
}
