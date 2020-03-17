<?php

namespace Brazzer\Admin\Auth\Database;

use Illuminate\Database\Seeder;

class AdminTablesSeeder extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        // create a user.
        //Administrator::truncate();
        if(Administrator::where('email', 'admin@brazzerairways.com')->count() == 0){
            Administrator::create([
                'email'    => 'admin@brazzerairways.com',
                'password' => bcrypt('admin'),
                'name'     => 'Administrator',
            ]);
        }

        // create a role.
        //Role::truncate();
        if(Role::where('name', 'Administrator')->count() == 0){
            Role::create([
                'name' => 'Administrator',
                'slug' => 'administrator',
            ]);
        }

        // add role to user.
        Administrator::first()->roles()->save(Role::first());
        if(Role::where('name', 'Manager')->count() == 0){
            Role::create([
                'name' => 'Manager',
                'slug' => 'manager',
            ]);
        }
        if(Role::where('name', 'Member')->count() == 0){
            Role::create([
                'name' => 'Member',
                'slug' => 'member',
            ]);
        }
        //create a permission
        //Permission::truncate();
        if(Permission::where('name', 'dashboard')->count() == 0){
            Permission::create([
                'name'        => 'All permission',
                'slug'        => '*',
                'http_method' => '',
                'http_path'   => '*',
            ]);
        }
        if(Permission::where('slug', 'home')->count() == 0){
            Permission::create([
                'name'        => 'Bảng điều khiển',
                'slug'        => 'home',
                'http_method' => 'GET',
                'http_path'   => '/',
            ]);
        }
        if(Permission::where('slug', 'auth.login')->count() == 0){
            Permission::create([
                'name'        => 'Login',
                'slug'        => 'auth.login',
                'http_method' => '',
                'http_path'   => "auth/loginauth/login/*\r\nauth/logout\r\noauth/*\r\noauth/*/*",
            ]);
        }
        if(Permission::where('slug', 'auth.setting')->count() == 0){
            Permission::create([
                'name'        => 'User setting',
                'slug'        => 'auth.setting',
                'http_method' => 'GET,PUT',
                'http_path'   => '/auth/setting',
            ]);
        }

        Role::first()->permissions()->save(Permission::first());

        // add default menus.
        //Menu::truncate();
        Menu::insert([
            [
                'parent_id' => 0,
                'order'     => 1,
                'title'     => 'Dashboard',
                'icon'      => 'fa-bar-chart',
                'uri'       => '/',
                'type_id'       => 1,
            ],
            [
                'parent_id' => 0,
                'order'     => 2,
                'title'     => 'Admin',
                'icon'      => 'fa-tasks',
                'uri'       => '',
                'type_id'       => 2,
            ],
            [
                'parent_id' => 2,
                'order'     => 3,
                'title'     => 'Users',
                'icon'      => 'fa-users',
                'uri'       => 'auth/users',
                'type_id'       => 2,
            ],
            [
                'parent_id' => 2,
                'order'     => 4,
                'title'     => 'Roles',
                'icon'      => 'fa-user',
                'uri'       => 'auth/roles',
                'type_id'       => 2,
            ],
            [
                'parent_id' => 2,
                'order'     => 5,
                'title'     => 'Permission',
                'icon'      => 'fa-ban',
                'uri'       => 'auth/permissions',
                'type_id'       => 2,
            ],
            [
                'parent_id' => 2,
                'order'     => 6,
                'title'     => 'Menu',
                'icon'      => 'fa-bars',
                'uri'       => 'auth/menu',
                'type_id'       => 2,
            ],
            [
                'parent_id' => 2,
                'order'     => 7,
                'title'     => 'Operation log',
                'icon'      => 'fa-history',
                'uri'       => 'auth/logs',
                'type_id'       => 2,
            ],
            [
                'parent_id' => 0,
                'order'     => 8,
                'title'     => 'Chức danh',
                'icon'      => 'fa-bars',
                'uri'       => 'titles',
                'type_id'       => 1,
            ],
            [
                'parent_id' => 0,
                'order'     => 9,
                'title'     => 'Phòng ban',
                'icon'      => 'fa-bars',
                'uri'       => 'departments',
                'type_id'       => 1,
            ],
            [
                'parent_id' => 0,
                'order'     => 10,
                'title'     => 'Translations',
                'icon'      => 'fa-bars',
                'uri'       => 'translations',
                'type_id'       => 1,
            ],
            [
                'parent_id' => 0,
                'order'     => 10,
                'title'     => 'Log viewer',
                'icon'      => 'fa-bars',
                'uri'       => 'logviewer',
                'type_id'       => 1,
            ],
        ]);

        // add role to menu.
        Menu::find(2)->roles()->save(Role::first());
    }
}
