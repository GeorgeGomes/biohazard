<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRequestTableBaseFee extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('request', function(Blueprint $table) {
            $table->integer('is_base_fee_paid');
            $table->integer('is_cancel_fee_paid');
            $table->dateTime('provider_acceptance_time');
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
