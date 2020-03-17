<?php

namespace Brazzer\Admin\Form\Field;

use Brazzer\Admin\Facades\Admin;
use Brazzer\Admin\Form\Field;

class Summernote extends Field{
    protected $view = 'admin::form.editor';
    protected static $js = [];

    public function render(){

        $name = $this->formatName($this->column);
        //$config = (array)Summernote::config('config');
        $config = (array)config('admin.fields.summernote');
        $config = array_merge([
            'lang'   => 'vi-VN',
            'height' => 250,
        ], $config);
        Admin::js("brazzer-admin/summernote-editor/lang/summernote-{$config['lang']}.js");
        //$config = json_encode($config);

        $this->script = <<<EOT
$('#{$this->id}').summernote({
    "lang":"{$config['lang']}",
    "height":{$config['height']},
    callbacks: {
        onPaste: function (e) {
            var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
            e.preventDefault();
            document.execCommand('insertText', false, bufferText);
        }
    }
});
$('#{$this->id}').on("summernote.change", function (e) {
    var html = $('#{$this->id}').summernote('code');
    $('input[name="{$name}"]').val(html);
});
EOT;
        return parent::render();
    }
}
