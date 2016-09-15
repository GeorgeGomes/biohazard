<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('transaction', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', array('base_tax', 'cancel_tax', 'request_price'));
            $table->enum('status', array('processing', 'authorized', 'paid', 'waiting_payment', 'pending_refund', 'refunded', 'refused'));
            $table->float('gross_value');
            $table->float('net_value');
            $table->float('provider_value');
            $table->float('gateway_tax_value');
            $table->string('gateway_transaction_id');
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
		//
	}

}
