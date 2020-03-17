<?php

namespace Brazzer\Admin\Form\Field;

class Date extends Text
{
    protected static $css = [
        '/brazzer-admin/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
    ];

    protected static $js = [
        '/brazzer-admin/moment/min/moment-with-locales.min.js',
        '/brazzer-admin/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
    ];

    protected $format = 'YYYY-MM-DD';

    public function format($format)
    {
        $this->format = $format;

        return $this;
    }

    public function prepare($value)
    {
        if ($value === '') {
            $value = null;
        }

        return $value;
    }

    public function render()
    {
        $this->options['format'] = $this->format;
        $this->options['locale'] = array_key_exists('locale', $this->options) ? $this->options['locale'] : config('app.locale');
        $this->options['allowInputToggle'] = true;

        //hongnn
        $class = $this->getElementClassSelector();
        $options = json_encode($this->options);
        $this->script = <<<EOT
$("{$class}").parent().datetimepicker({$options});
$('input{$class}').bind('keyup keypress keydown paste',function () {
    $(this).val('');
});
EOT;

        $this->prepend('<i class="fa fa-calendar fa-fw"></i>')
            ->defaultAttribute('style', 'width: 110px');

        return parent::render();
    }
}
