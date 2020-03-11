<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkCustomScriptTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_custom_script', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // use for foreign key support

            $table->increments('id');            
            $table->integer('link_id')->unsigned();
            $table->integer('custom_script_id')->unsigned();

            $table->foreign('link_id')->references('id')->on('links')->onDelete('cascade');
            $table->foreign('custom_script_id')->references('id')->on('custom_scripts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('link_custom_script');
    }
}
