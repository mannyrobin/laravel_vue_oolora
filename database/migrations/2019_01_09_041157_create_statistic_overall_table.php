<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatisticOverallTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistic_overall', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // use for foreign key support

            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('clicks')->nullable();
            $table->integer('unique_clicks')->nullable();
            $table->integer('conversion')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statistic_overall');
    }
}
