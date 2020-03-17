<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdminSiteConfigTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        $database_table = config('admin.database.site_config_table', 'site_config');
        if(!Schema::hasTable($database_table)){
            Schema::create($database_table, function(Blueprint $table){
                $table->text('value');
                $table->string('name');
                $table->index([
                    'name'
                ]);
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
        Schema::dropIfExists(config('admin.database.site_config_table', 'admin_notifies'));
    }
}
