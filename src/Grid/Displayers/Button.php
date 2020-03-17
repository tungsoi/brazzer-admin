<?php

namespace Brazzer\Admin\Grid\Displayers;

class Button extends AbstractDisplayer{
    public function display($style = ''){
        $form_style = config('admin.form-style');
        $style = collect((array)$style)->map(function($style){
            return 'btn-' . $style;
        })->implode(' ');

        return "<span class='btn btn-{$form_style} $style'>{$this->value}</span>";
    }
}
