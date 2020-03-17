<?php

namespace Brazzer\Admin\Form;

use Brazzer\Admin\Form;
use Illuminate\Support\Collection;

class Column{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var Collection
     */
    protected $columns;
    protected $width;

    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * column constructor.
     *
     * @param Form $form
     */
    public function __construct(Form $form){
        $this->form = $form;

        $this->columns = new Collection();
    }

    /**
     * Append a column section.
     *
     * @param string $title
     * @param \Closure $content
     * @param bool $active
     *
     * @return $this
     */
    public function append($width, \Closure $content){
        $this->width = $width = $width < 1 ? round(12 * $width) : $width;
        $fields = $this->collectFields($content);
        $this->columns->push(compact('width', 'fields'));
        return $this;
    }

    /**
     * Collect fields under current column.
     *
     * @param \Closure $content
     *
     * @return Collection
     */
    protected function collectFields(\Closure $content){
        call_user_func($content, $this->form);
        $fields = clone $this->form->builder()->fields();
        $all = $fields->toArray();
        foreach($this->form->rows as $row){
            $rowFields = array_map(function($field){
                return $field['element'];
            }, $row->getFields());
            $match = false;
            foreach($rowFields as $field){
                if(($index = array_search($field, $all)) !== false){
                    if(!$match){
                        $fields->put($index, $row);
                    }else{
                        $fields->pull($index);
                    }
                    $match = true;
                }
            }
        }
        $fields = $fields->slice($this->offset);
        $this->offset += $fields->count();
        return $fields;
    }

    /**
     * Get all columns.
     *
     * @return Collection
     */
    public function width(){
        return $this->width;
    }
    public function getColumns(){
        // If there is no active column, then active the first.
        /*if($this->columns->filter(function($column){
            return $column['active'];
        })->isEmpty()){
            $first = $this->columns->first();
            $first['active'] = true;
            $this->columns->offsetSet(0, $first);
        }*/
        return $this->columns;
    }

    /**
     * @return bool
     */
    public function isEmpty(){
        return $this->columns->isEmpty();
    }
}
