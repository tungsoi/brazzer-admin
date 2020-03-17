<?php

namespace Brazzer\Admin\Tree\Filter;

class EndsWith extends Like
{
    protected $exprFormat = '%{value}';
}
