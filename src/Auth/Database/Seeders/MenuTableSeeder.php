<?php

namespace Brazzer\Admin\Auth\Database\Seeders;

use Brazzer\Admin\Auth\Database\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MenuTableSeeder extends Seeder{
    public function run(){
        $database_table = config('admin.database.menu_table');
        if(Schema::hasTable($database_table)){
            if(Menu::where('uri', 'titles')->count() == 0){
                Menu::create([
                    'parent_id' => 0,
                    'title'     => 'Chức danh',
                    'icon'      => 'fa-bars',
                    'uri'       => 'titles',
                    'type_id'   => 1,
                ]);
            }
            if(Menu::where('uri', 'departments')->count() == 0){
                Menu::create([
                    'parent_id' => 0,
                    'title'     => 'Phòng ban',
                    'icon'      => 'fa-bars',
                    'uri'       => 'departments',
                    'type_id'   => 1,
                ]);
            }
            if(Menu::where('uri', 'translations')->count() == 0){
                Menu::create([
                    'parent_id' => 0,
                    'title'     => 'Translations',
                    'icon'      => 'fa-bars',
                    'uri'       => 'translations',
                    'type_id'   => 2,
                ]);
            }
            if(Menu::where('uri', 'logviewer')->count() == 0){
                Menu::create([
                    'parent_id' => 0,
                    'title'     => 'Log viewer',
                    'icon'      => 'fa-bars',
                    'uri'       => 'logviewer',
                    'type_id'   => 2,
                ]);
            }
        }
    }
}
