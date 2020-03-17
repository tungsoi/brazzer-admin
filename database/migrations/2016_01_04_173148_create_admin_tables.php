<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminTables extends Migration{
    /**
     * {@inheritdoc}
     */
    public function getConnection(){
        return config('admin.database.connection') ?: config('database.default');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        if(!Schema::hasTable(config('admin.database.users_table'))){
            Schema::create(config('admin.database.users_table'), function(Blueprint $table){
                $table->increments('id');
                $table->string('email', 190)->unique();
                $table->string('password', 60)->nullable();
                $table->string('name')->nullable();
                $table->string('avatar')->nullable();
                $table->string('remember_token', 100)->nullable();
                $table->string('provider_id', 190)->nullable();
                $table->string('provider', 50)->nullable();
                $table->tinyInteger('is_social')->nullable();
                $table->date('created_date')->nullable();
                $table->text('token')->nullable();
                $table->timestamps();
            });
        }
        if(!Schema::hasTable(config('admin.database.roles_table'))){
            Schema::create(config('admin.database.roles_table'), function(Blueprint $table){
                $table->increments('id');
                $table->string('name', 50)->unique();
                $table->string('slug', 50)->unique();
                $table->timestamps();
            });
        }
        if(!Schema::hasTable(config('admin.database.permissions_table'))){
            Schema::create(config('admin.database.permissions_table'), function(Blueprint $table){
                $table->increments('id');
                $table->string('name', 50)->unique();
                $table->string('slug', 50)->unique();
                $table->string('http_method')->nullable();
                $table->text('http_path')->nullable();
                $table->integer('permission_id')->default(0)->nullable();
                $table->timestamps();
            });
        }
        if(!Schema::hasTable(config('admin.database.menu_table'))){
            Schema::create(config('admin.database.menu_table'), function(Blueprint $table){
                $table->increments('id');
                $table->integer('parent_id')->default(0);
                $table->integer('order')->default(0)->nullable();
                $table->string('title', 50);
                $table->tinyInteger('type_id')->nullable();
                $table->tinyInteger('is_header')->default(0)->nullable();
                $table->tinyInteger('is_active')->default(0)->nullable();
                $table->string('icon', 50)->nullable();
                $table->string('uri', 50)->nullable();
                $table->string('permission')->nullable();

                $table->timestamps();
            });
        }
        if(!Schema::hasTable(config('admin.database.role_users_table'))){
            Schema::create(config('admin.database.role_users_table'), function(Blueprint $table){
                $table->integer('role_id');
                $table->integer('user_id');
                $table->index([
                    'role_id',
                    'user_id'
                ]);
                $table->timestamps();
            });
        }
        if(!Schema::hasTable(config('admin.database.role_permissions_table'))){
            Schema::create(config('admin.database.role_permissions_table'), function(Blueprint $table){
                $table->integer('role_id');
                $table->integer('permission_id');
                $table->index([
                    'role_id',
                    'permission_id'
                ]);
                $table->timestamps();
            });
        }
        if(!Schema::hasTable(config('admin.database.user_permissions_table'))){
            Schema::create(config('admin.database.user_permissions_table'), function(Blueprint $table){
                $table->integer('user_id');
                $table->integer('permission_id');
                $table->index([
                    'user_id',
                    'permission_id'
                ]);
                $table->timestamps();
            });
        }
        if(!Schema::hasTable(config('admin.database.role_menu_table'))){
            Schema::create(config('admin.database.role_menu_table'), function(Blueprint $table){
                $table->integer('role_id');
                $table->integer('menu_id');
                $table->index([
                    'role_id',
                    'menu_id'
                ]);
                $table->timestamps();
            });
        }
        if(!Schema::hasTable(config('admin.database.role_menu_table'))){
            Schema::create(config('admin.database.operation_log_table'), function(Blueprint $table){
                $table->increments('id');
                $table->integer('user_id')->nullable();
                $table->string('path')->nullable();
                $table->string('method', 10)->nullable();
                $table->string('ip')->nullable();
                $table->text('input')->nullable();
                $table->index('user_id')->nullable();
                $table->timestamps();
            });
        }
        if(!Schema::hasTable(config('admin.database.translations_table', 'admin_translations'))){
            Schema::create(config('admin.database.translations_table', 'admin_translations'), function(Blueprint $table){
                $table->increments('id');
                $table->integer('status')->default(0)->nullable();
                $table->string('locale')->nullable();
                $table->string('group')->nullable();
                $table->text('key')->nullable();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists(config('admin.database.users_table'));
        Schema::dropIfExists(config('admin.database.roles_table'));
        Schema::dropIfExists(config('admin.database.permissions_table'));
        Schema::dropIfExists(config('admin.database.menu_table'));
        Schema::dropIfExists(config('admin.database.user_permissions_table'));
        Schema::dropIfExists(config('admin.database.role_users_table'));
        Schema::dropIfExists(config('admin.database.role_permissions_table'));
        Schema::dropIfExists(config('admin.database.role_menu_table'));
        Schema::dropIfExists(config('admin.database.operation_log_table'));
        Schema::dropIfExists(config('admin.database.translations_table', 'admin_translations'));
    }
}
