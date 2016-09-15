<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProviderService extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('provider_services', function ($table) {
    		$table->integer('category')->unsigned();
    		$table->foreign('category')->references('id')->on('provider_category');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('provider_services', function($table)
		{
			$table->dropColumn('category');
		});
	}

}
