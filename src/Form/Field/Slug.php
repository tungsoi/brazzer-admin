<?php

namespace Brazzer\Admin\Form\Field;

use Brazzer\Admin\Facades\Admin;

class Slug extends Text
{
    protected $url_frontend;
    protected $parent_id;


    public function parent($string = 'title')
    {
        $this->parent_id = $string;
    }

    public function url_frontend($url = '')
    {
        $this->url_frontend = $url;
    }

    public function render()
    {
        $url_frontend = $this->url_frontend;
        if ($url_frontend == '') {
            $url_frontend = url('/') . '/';
        }
        $parent_id = $this->parent_id ?: $this->column;
        $group_class = 'form-group-' . $this->column;
        $this->setGroupClass(($parent_id != $this->column ? 'hide ' : '') . $group_class);
        $script = '
            var url_root = "' . $url_frontend . '";
            var this_slug = $("input.' . $this->column . '").val();
            $("input.' . $this->column . '").val(removeAccents(this_slug));';

        $html = '<span class=\"help-block\">" + url_root + "<span id=\"view_slug-' . $this->id . '\">" + removeAccents(this_slug) + "</span>.html ';
        if ($parent_id != $this->column) {
            $html .= '<a href=\"#\" id=\"form_slug-' . $this->id . '\" noloading>Chỉnh sửa</a></span>';
        }
        $script .= 'var parent_slug = $("input.' . $parent_id . '").parents(".input-group");
            parent_slug.after("' . $html . '");
            $(document).on("keypress keyup keydown keychange click", "input.' . $parent_id . '", function () {
                if($.trim(this_slug) == ""){
                    var slug = removeAccents(this.value);
                    $("input.' . $this->column . '").val(slug);
                    $("#view_slug-' . $this->column . '").text(slug);
                }
            });';

        $script .= '
            $(document).on("keypress keyup keydown keychange click", "input.' . $this->column . '", function () {
                var slug = removeAccents(this.value);
                $("input.' . $this->column . '").val(slug);
                $("#view_slug-' . $this->column . '").text(slug);
            });
            $(document).on("click", "#form_slug-' . $this->id . '", function () {
                if($(".' . $group_class . '").hasClass("hide")){
                    $(".' . $group_class . '").removeClass("hide");
                }else{
                    $(".' . $group_class . '").addClass("hide");
                }
                return false;
            });
            function removeAccents(str) {
                var AccentsMap = [
                    "aàảãáạăằẳẵắặâầẩẫấậ",
                    "AÀẢÃÁẠĂẰẲẴẮẶÂẦẨẪẤẬ",
                    "dđ", "DĐ",
                    "eèẻẽéẹêềểễếệ",
                    "EÈẺẼÉẸÊỀỂỄẾỆ",
                    "iìỉĩíị",
                    "IÌỈĨÍỊ",
                    "oòỏõóọôồổỗốộơờởỡớợ",
                    "OÒỎÕÓỌÔỒỔỖỐỘƠỜỞỠỚỢ",
                    "uùủũúụưừửữứự",
                    "UÙỦŨÚỤƯỪỬỮỨỰ",
                    "yỳỷỹýỵ",
                    "YỲỶỸÝỴ"
                ];
                for (var i = 0; i < AccentsMap.length; i++) {
                    var re = new RegExp(\'[\' + AccentsMap[i].substr(1) + \']\', \'g\');
                    var char = AccentsMap[i][0];
                    str = str.replace(re, char);
                }
                str = str.replace(/\W+/g, \'-\').toLowerCase();
                str = str.replace(/\s+/g, \'-\').toLowerCase();
                str = str.replace(/---/g, \'-\').toLowerCase();
                str = str.replace(/--/g, \'-\').toLowerCase();
                return str;
            }
        ';
        Admin::script($script);
        return parent::render();
    }
}
