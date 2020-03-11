<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinkCallToActionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('link_call_to_action', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // use for foreign key support

            $table->increments('id');            
            $table->integer('link_id')->unsigned();
            $table->integer('call_to_action_id')->unsigned();
            
            $table->foreign('link_id')->references('id')->on('links')->onDelete('cascade');
            $table->foreign('call_to_action_id')->references('id')->on('call_to_actions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('link_call_to_action');
    }
}
