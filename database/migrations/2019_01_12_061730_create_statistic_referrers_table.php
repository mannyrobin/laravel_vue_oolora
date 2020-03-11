<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStatisticReferrersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistic_referrers', function (Blueprint $table) {
            $table->engine = 'InnoDB'; // use for foreign key support

            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('link_id')->unsigned();
            $table->string('referrer_url')->nullable();
            $table->integer('count')->nullable();

            $table->foreign('link_id')->references('id')->on('links')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statistic_referrers');
    }
}
