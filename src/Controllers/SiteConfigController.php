<?php

namespace Brazzer\Admin\Controllers;

use Brazzer\Admin\Auth\Database\SiteConfig;
use Brazzer\Admin\Facades\Admin;
use Brazzer\Admin\Form\Footer;
use Brazzer\Admin\Form\Tools;
use Brazzer\Admin\Layout\Content;
use Brazzer\Admin\Controllers\Form\ConfigForm;
use Illuminate\Routing\Controller;

class SiteConfigController extends Controller{
    public $publicFieldFoo = [
        'default',
        'attribute',
        'help',
        'placeholder',
        'rules',
        'options',
        'rows',
        'format',
        'states',
        'symbol',
        'max',
        'min',
        'uniqueName',
        'removable',
        'stacked'
    ];
    public $rangeFoo = [
        'timeRange',
        'dateRange',
        'datetimeRange'
    ];

    public function index(Content $content){
        return $content->header('Site Config')->description(config('admin.description'))->body($this->form()->configEdit());
    }

    protected function form(){
        $tabs = config('site_config.site_config_groups');
        $permissions = config('site_config.site_config_permissions');
        $form = new ConfigForm(new SiteConfig());
        if($tabs){
            foreach($tabs as $prefix => $title){
                // Skip building the tab if no permission
                if(!Admin::user()->isAdministrator() && !empty($permissions[$prefix]) && !Admin::user()->inRoles($permissions[$prefix])){
                    continue;
                }
                // When prefixes are configured only, the label key value can be undefined
                if(is_numeric($prefix) && is_string($title)){
                    $prefix = $title;
                }
                $fields = config('site_config.' . $prefix);
                $form->tab($title, function(ConfigForm $form) use ($fields, $prefix){
                    foreach($fields as $name => $settings){
                        // When only the field name is configured, the field key value can be undefined
                        if(is_numeric($name) && is_string($settings)){
                            $name = $settings;
                            $settings = [];
                        }
                        // The field type must have type as the key name
                        $fieldType = isset($settings['type']) ? $settings['type'] : 'text';
                        $fieldName = $prefix . ConfigForm::SEPARATOR . $name;
                        unset($settings['type']);
                        // Determine whether the field type is supported
                        if(isset($form::$availableFields[$fieldType])){
                            foreach($settings as $settingKey => $settingValue){
                                // Filter out serpentine invocation methods in the configuration to support single or no arguments
                                $key = $settingKey;
                                // Snake methods with no parameters
                                if(is_numeric($settingKey) && is_string($settingValue)){
                                    $settingKey = $settingValue;
                                }
                                if(in_array($settingKey, $this->getFieldFoo())){
                                    if($settingKey == $settingValue){
                                        $snakelikes[$settingValue] = $settingValue;
                                    }else{
                                        $snakelikes[$settingKey] = $settingValue;
                                    }
                                    unset($settings[$key]);
                                }
                                // Filter out the callback method
                                if($settingValue instanceof \Closure){
                                    $callbacks[] = $settingValue;
                                    unset($settings[$key]);
                                }
                            }
                            // Build the field with the remaining parameters
                            $settings = array_values($settings);
                            if(in_array($fieldType, $this->rangeFoo)){
                                $fieldNameEnd = $fieldName . ConfigForm::SEPARATOR . 'end';
                                $fieldName = $fieldName . ConfigForm::SEPARATOR . 'start';
                                array_unshift($settings, $fieldNameEnd);
                            }
                            $field = $form->$fieldType($fieldName, ...$settings);
                            // Call the snake method
                            if(isset($snakelikes)){
                                foreach($snakelikes as $foo => $params){
                                    if($foo == $params){
                                        $field->$foo();
                                    }else{
                                        $field->$foo($params);
                                    }
                                }
                                unset($snakelikes);
                            }
                            // Call the callback method
                            if(isset($callbacks)){
                                foreach($callbacks as $callback){
                                    call_user_func($callback, $field);
                                }
                                unset($callback);
                            }
                        }
                    }
                });
            }
        }
        $form->setAction(route('admin.siteconfig.index'));
        $form->tools(function(Tools $tools){
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableView();
        });
        $form->footer(function(Footer $footer){
            $footer->disableReset();
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
        });
        $form->setTitle(config('admin.extensions.site_config.action', ' '));
        return $form;
    }

    public function update(){
        return $this->form()->configUpdate();
    }

    protected function getFieldFoo(){
        return $this->publicFieldFoo;
    }
}