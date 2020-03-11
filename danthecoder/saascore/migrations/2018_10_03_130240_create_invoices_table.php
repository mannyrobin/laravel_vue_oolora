<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->integer('subscription_id')->unsigned()->index();
            $table->string('invoice_number')->unique()->index();
            $table->string('gateway_charge_id')->nullable();
            $table->decimal('total', 7, 2);
            $table->decimal('subtotal', 7, 2);
            $table->char('currency', 3);
            $table->string('payment_gateway');
            $table->string('paypal_email')->nullable();
            $table->string('card_brand')->nullable();
            $table->char('card_last4', 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
