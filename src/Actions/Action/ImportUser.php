<?php

namespace Brazzer\Admin\Actions\Action;

use Brazzer\Admin\Actions\Action;
use Brazzer\Admin\Auth\Database\Administrator;
use Brazzer\Admin\Auth\Database\UserInfo;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportUser extends Action{
    public $name = 'titles.header';
    protected $selector = '.import-users';

    public function handle(Request $request){
        $allowed = array(
            'xls',
            'csv',
            'xlsx',
            'vnd.ms-excel',
            'vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );
        if($request->hasFile('users')){
            $file = $request->file('users');
            $extension = $file->getClientOriginalExtension();
            if(in_array($extension, $allowed)){
                $name = $file->getClientOriginalName();
                $fileExtension = $file->getClientOriginalExtension(); // Lấy . của file
                // Filename cực shock để khỏi bị trùng
                $fileName = time() . "_" . rand(0, 9999999) . "_" . md5(rand(0, 9999999)) . "." . $fileExtension;
                $folder_name = ltrim($this->selector, '.');
                $imagePath = 'uploads/' . $folder_name . '/' . date('Y/');
                $path = storage_path($imagePath);
                \File::isDirectory($path) or \File::makeDirectory($path, 0777, true, true);
                if($file->move($path, $fileName)){
                    if($this->import($path)){
                        return $this->response()->success("Import thành công : {$name}")->refresh();
                    }
                }
            }
        }
        return $this->response()->error("Import khong thành công : {$name}")->refresh();
    }

    protected function import($path){
        $data = Excel::load($path, function($reader){
            $reader->setHeaderRow(1);
        })->get();
        if(!empty($data) && $data->count()){
            foreach($data->toArray() as $k => $item){
                $field = [
                    'email' => $item['email']
                ];
                if($user = Administrator::where('email', $field['email'])->first()){
                    $user->update($field);
                }else{
                    $user = Administrator::create($field);
                }
                $field_userinfo = ['user_id' => $user->id];
                if($userinfo = UserInfo::where('user_id', $field_userinfo['user_id'])->first()){
                    $userinfo->update($field_userinfo);
                }else{
                    UserInfo::create($field_userinfo);
                }
            }
            return true;
        }
        return false;
    }

    public function form(){
        $this->file('users', 'File danh sách');
    }

    public function html(){
        $name = trans($this->name);
        $selector = ltrim($this->selector, '.');
        $form_style = config('admin.form-style');
        return <<<HTML
        <a class="btn btn-{$form_style} btn-default {$selector}" noloading>Import {$name}</a>
HTML;
    }
}