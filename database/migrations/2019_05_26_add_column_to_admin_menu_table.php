<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ColumnToAdminMenuTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        $database_table = config('admin.database.menu_table');
        Schema::table($database_table, function(Blueprint $table) use ($database_table){
            if(!Schema::hasColumn($database_table, 'is_active')){
                $table->tinyInteger('is_active');
            }
            if(!Schema::hasColumn($database_table, 'is_header')){
                $table->tinyInteger('is_header');
            }
            if(!Schema::hasColumn($database_table, 'type_id')){
                $table->tinyInteger('type_id');
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        $database_table = config('admin.database.menu_table');
        Schema::table('admin_users', function(Blueprint $table) use ($database_table){
            if(Schema::hasColumn($database_table, 'is_active')){
                $table->dropColumn('is_active');
            }
            if(Schema::hasColumn($database_table, 'is_header')){
                $table->dropColumn('is_header');
            }
            if(Schema::hasColumn($database_table, 'type_id')){
                $table->dropColumn('type_id');
            }
        });
    }
}
