<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkPixelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_pixel', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // use for foreign key support

            $table->increments('id');            
            $table->integer('link_id')->unsigned();
            $table->integer('pixel_id')->unsigned();
            
            $table->foreign('link_id')->references('id')->on('links')->onDelete('cascade');
            $table->foreign('pixel_id')->references('id')->on('pixels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('link_pixel', function (Blueprint $table) {
            //
        });
    }
}
