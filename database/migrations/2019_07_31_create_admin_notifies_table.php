<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdminNotifiesTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        $database_table = config('admin.database.notifies_table', 'admin_notifies');
        if(!Schema::hasTable($database_table)){
            Schema::create($database_table, function(Blueprint $table){
                $table->increments('id');
                $table->integer('user_id');
                $table->text('messenger');
                $table->tinyInteger('type');
                $table->tinyInteger('is_read');
                $table->string('link');
                $table->string('icon');
                $table->string('target');
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
        Schema::dropIfExists(config('admin.database.notifies_table', 'admin_notifies'));
    }
}
