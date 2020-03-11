<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Views extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
      Schema::create('views', function (Blueprint $table) {
          $table->engine = 'InnoDB'; // use for foreign key support

          $table->increments('id');
          $table->integer('link')->unsigned();
          $table->integer('user')->unsigned();
          $table->string('country');
          $table->string('browser');
          $table->string('os');
          $table->string('type');
          $table->string('ip');
          $table->string('user_agent');
          $table->string('referrer');
          $table->string('referrer_domain');
          $table->string('date_day');
          $table->string('date_month');
          $table->integer('created');

          $table->timestamps();

          $table->foreign('user')->references('id')->on('users')->onDelete('cascade');
          $table->foreign('link')->references('id')->on('links')->onDelete('cascade');
      });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
      Schema::dropIfExists('views');
  }
}
