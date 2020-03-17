<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ColumnToAdminUsersTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        $database_table = config('admin.database.users_table');
        Schema::table($database_table, function(Blueprint $table) use ($database_table){
            if(!Schema::hasColumn($database_table, 'provider_id')){
                $table->string('provider_id', 190)->unique();
            }
            if(!Schema::hasColumn($database_table, 'provider')){
                $table->string('provider', 50);
            }
            if(!Schema::hasColumn($database_table, 'is_social')){
                $table->tinyInteger('is_social');
            }
            if(!Schema::hasColumn($database_table, 'created_date')){
                $table->date('created_date');
            }
            if(!Schema::hasColumn($database_table, 'token')){
                $table->text('token');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        $database_table = config('admin.database.users_table');
        Schema::table($database_table, function(Blueprint $table) use ($database_table){
            if(Schema::hasColumn($database_table, 'provider_id')){
                $table->dropColumn('provider_id');
            }
            if(Schema::hasColumn($database_table, 'provider')){
                $table->dropColumn('provider');
            }
            if(Schema::hasColumn($database_table, 'is_social')){
                $table->dropColumn('is_social');
            }
            if(Schema::hasColumn($database_table, 'created_date')){
                $table->dropColumn('created_date');
            }
            if(Schema::hasColumn($database_table, 'token')){
                $table->dropColumn('token');
            }
        });
    }
}
