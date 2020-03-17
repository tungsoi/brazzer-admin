<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ColumnToAdminPermissionsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        $database_table = config('admin.database.permissions_table');
        Schema::table($database_table, function(Blueprint $table) use ($database_table){
            if(!Schema::hasColumn($database_table, 'permission_id')){
                $table->integer('permission_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        $database_table = config('admin.database.permissions_table');
        Schema::table($database_table, function(Blueprint $table) use ($database_table){
            if(Schema::hasColumn($database_table, 'permission_id')){
                $table->dropColumn('permission_id');
            }
        });
    }
}
