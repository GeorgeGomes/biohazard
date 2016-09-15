<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('request_options', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('provider_service_id')->unsigned();
    		$table->foreign('provider_service_id')->references('id')->on('provider_services');
			$table->integer('request_id')->unsigned();
    		$table->foreign('request_id')->references('id')->on('request');
			$table->string('vehicle_brand');
			$table->string('vehicle_plate');
			$table->string('vehicle_observations');
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
		Schema::drop('request_options');
	}

}
