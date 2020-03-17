<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdminTranslationsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        $database_table = config('admin.database.translations_table', 'admin_translations');
        if(!Schema::hasTable($database_table)){
            Schema::create($database_table, function(Blueprint $table){
                $table->increments('id');
                $table->string('locale');
                $table->string('group');
                $table->text('key');
                $table->text('value');
                $table->tinyInteger('status');
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
        Schema::dropIfExists(config('admin.database.translations_table', 'admin_translations'));
    }
}
