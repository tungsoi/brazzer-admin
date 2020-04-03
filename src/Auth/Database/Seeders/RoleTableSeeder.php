<?php

namespace Brazzer\Admin\Auth\Database\Seeders;

use Brazzer\Admin\Auth\Database\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RoleTableSeeder extends Seeder{
    public function run(){
        $database_table = config('admin.database.roles_table');
        if(Schema::hasTable($database_table)){
            if(Role::where('slug', 'manager')->count() == 0){
                Role::create([
                    'name' => 'Manager',
                    'slug' => 'manager',
                ]);
            }
            if(Role::where('slug', 'member')->count() == 0){
                Role::create([
                    'name' => 'Member',
                    'slug' => 'member',
                ]);
            }
        }
    }
}
