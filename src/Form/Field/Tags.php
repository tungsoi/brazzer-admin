<?php

namespace Brazzer\Admin\Form\Field;

use Brazzer\Admin\Form\Field;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Tags extends Field
{
    /**
     * @var array
     */
    protected $value = [];

    /**
     * @var bool
     */
    protected $keyAsValue = false;

    protected $sortable = false;

    /**
     * @var string
     */
    protected $visibleColumn = null;

    /**
     * @var string
     */
    protected $key = null;

    /**
     * @var \Closure
     */
    protected $saveAction = null;

    /**
     * @var array
     */
    protected static $css = [
        '/brazzer-admin/AdminLTE/plugins/select2/select2.min.css',
    ];

    /**
     * @var array
     */
    protected static $js = [
        '/brazzer-admin/AdminLTE/plugins/select2/select2.full.min.js',
        //'https://code.jquery.com/ui/1.12.1/jquery-ui.js',
    ];

    /**
     * {@inheritdoc}
     */
    public function fill($data)
    {
        $this->value = Arr::get($data, $this->column);

        if (is_array($this->value) && $this->keyAsValue) {
            $this->value = array_column($this->value, $this->visibleColumn, $this->key);
        }

        if (is_string($this->value)) {
            $this->value = explode(',', $this->value);
        }

        $this->value = array_filter((array) $this->value, 'strlen');
    }

    /**
     * Set visible column and key of data.
     *
     * @param $visibleColumn
     * @param $key
     *
     * @return $this
     */
    public function pluck($visibleColumn, $key)
    {
        if (!empty($visibleColumn) && !empty($key)) {
            $this->keyAsValue = true;
        }

        $this->visibleColumn = $visibleColumn;
        $this->key = $key;

        return $this;
    }

    /**
     * Set the field options.
     *
     * @param array|Collection|Arrayable $options
     *
     * @return $this|Field
     */
    public function options($options = [])
    {
        if (!$this->keyAsValue) {
            return parent::options($options);
        }

        if ($options instanceof Collection) {
            $options = $options->pluck($this->visibleColumn, $this->key)->toArray();
        }

        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $this->options = $options + $this->options;

        return $this;
    }

    /**
     * Set save Action.
     *
     * @param \Closure $saveAction
     *
     * @return $this
     */
    public function saving(\Closure $saveAction)
    {
        $this->saveAction = $saveAction;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function prepare($value)
    {
        $value = array_filter($value, 'strlen');

        if ($this->keyAsValue) {
            return is_null($this->saveAction) ? $value : ($this->saveAction)($value);
        }

        if (is_array($value) && !Arr::isAssoc($value)) {
            $value = implode(',', $value);
        }

        return $value;
    }

    /**
     * Get or set value for this field.
     *
     * @param mixed $value
     *
     * @return $this|array|mixed
     */
    public function value($value = null)
    {
        if (is_null($value)) {
            return empty($this->value) ? ($this->getDefault() ?? []) : $this->value;
        }

        $this->value = (array) $value;

        return $this;
    }
    public function sortable($sortable = true){
        $this->sortable = $sortable;
    }
    /**
     * {@inheritdoc}
     */
    public function render()
    {
        //if(!$this->sortable) {
            $this->script = "$(\"{$this->getElementClassSelector()}\").select2({
                tags: true,
                tokenSeparators: [',']
            })";
        /*}else {
            $this->script = "$(\"{$this->getElementClassSelector()}\").select2({
                tags: true,
                tokenSeparators: [',']
            }).on(\"select2:select\", function (evt) {
                var id = evt.params.data.id;
                var element = $(this).children(\"option[value=\"+id+\"]\");
                moveElementToEndOfParent(element);
                $(this).trigger(\"change\");
            });
            var ele=$(\"{$this->getElementClassSelector()}\").parent().find(\"ul.select2-selection__rendered\");
                ele.sortable({
                    containment: 'parent',
                    update: function() {
                        orderSortedValues();
                    }
                });
                orderSortedValues = function() {
                    var value = ''
                    $(\"{$this->getElementClassSelector()}\").parent().find(\"ul.select2-selection__rendered\").children(\"li[title]\").each(function(i, obj){
                        var element = $(\"{$this->getElementClassSelector()}\").children('option').filter(function () { return $(this).html() == obj.title });
                        moveElementToEndOfParent(element)
                    });
                };
                moveElementToEndOfParent = function(element) {
                    var parent = element.parent();
                    element.detach();
                    parent.append(element);
                };
            ";
        }*/

        if ($this->keyAsValue) {
            $options = $this->value + $this->options;
        } else {
            $options = array_unique(array_merge($this->value, $this->options));
        }

        return parent::render()->with([
            'options'    => $options,
            'keyAsValue' => $this->keyAsValue,
        ]);
    }
}
