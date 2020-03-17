<?php

namespace Brazzer\Admin\Grid\Tools;


class ImportExcel extends AbstractTool{
    protected $title;
    protected $field;
    protected $class;
    protected $link_import;
    protected $select_option;

    public function __construct($field = 'excel', $title = 'Import file excel', $link_import = '', $class = 'btn-default', $select_option = []){
        $this->field         = $field;
        $this->title         = $title;
        $this->class         = $class;
        $this->link_import   = $link_import;
        $this->select_option = $select_option;
    }

    public function render(){
        $bnt_insert = trans('admin.create');
        $bnt_cancel = trans('admin.cancel');
        $select     = '';
        if($this->select_option){
            $select_title = 'Default';
            if(isset($this->select_option['title'])){
                $select_title = $this->select_option['title'];
            }
            $select   .= '<div class="form-group clearfix">
                        <label class="col-sm-2 control-label"> ' . $select_title . '</label>
                        <div class="col-sm-8">';
            $required = false;
            if(isset($this->select_option['required'])){
                $required = $this->select_option['required'];
            }
            if(isset($this->select_option['name'])){
                $select .= '<select name="' . $this->select_option['name'] . '" class="form-control form_import" data-required="' . $required . '">';
            }else{
                $select .= '<select name="' . $this->select_option['name'] . '">';
            }
            if(isset($this->select_option['title'])){
                $select .= '<option value="">' . trans('admin.choose') . ' ' . $this->select_option['title'] . '</option>';
            }
            if(isset($this->select_option['data'])){
                foreach($this->select_option['data'] as $val => $title){
                    $select .= '<option value="' . $val . '">' . $title . '</option>';
                }
            }
            $select .= '</select></div></div>';
        }
        $form_style = config('admin.form-style');
        return <<<EOT
<button type="button" class="btn btn-{$form_style} {$this->class}" data-toggle="modal" href="#modal-import-file-{$this->field}">{$this->title}</button>
<div class="modal fade" id="modal-import-file-{$this->field}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">{$this->title}</h4>
                </div>
                <div class="modal-body clearfix">
                    <div class="form-group clearfix">
                        <div class="progress-upload-import">
                            <div id="progress" class="progress" data-file-name="">
                                <div class="progress-bar progress-bar-success" style="width: 0%;"></div>
                            </div>
                        </div>
                    </div>
                    <div id="filename_import" style="margin-bottom:10px"></div>
                    <div class="alert alert-danger" style="display: none;"></div>
                    <button type="button" class="btn btn-primary btn-{$form_style} apply-import-file" onclick="CMS.upload_import(this, '{$this->field}');">Upload && Import</button>
                    <button type="button" class="btn btn-default btn-{$form_style}" data-dismiss="modal">{$bnt_cancel}</button>
                    <form id="upload_import-{$this->field}" action="{$this->link_import}" method="post" enctype="multipart/form-data" style="display: none">
                        <div class="box-drop-drag" id="drop" style="display: block;">
                            <input type="file" multiple="true" accept=".xls,.csv,.xlsx" name="file" value="" class="hide" id="inp_upload"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="modal-import-data-{$this->field}">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">{$this->title}</h4>
                </div>
                <div class="modal-body clearfix"></div>
                <div class="modal-footer clearfix">
                    <button type="submit" id="btn_import_data_{$this->field}">{$bnt_insert}</button>
                </div>
            </div>
        </div>
    </div>
EOT;
    }
}
