<?php

namespace Brazzer\Admin\Grid\Displayers;


class TitleLink extends AbstractDisplayer{
    public function display(\Closure $callback = null, $title = '', $params = [], $router = 'home.index'){
        $row = $this->row;
        $new_param = [];
        foreach($params as $key => $val){
            if(isset($row->$val))
                $new_param[$key] = $row->$val;
        }
        $html = '<a href="' . route($router, $new_param) . '" noloading>' . (isset($row->$title) ? $row->$title : '') . '</a>';
        return <<<EOT
        {$html}
EOT;
    }
}