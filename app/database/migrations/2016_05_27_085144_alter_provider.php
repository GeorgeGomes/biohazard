<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProvider extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('provider', function(Blueprint $table)
		{
			$table->integer('status_id')->references('id')->on('provider_status');
			$table->string('address_number');
			$table->string('address_complements');
			$table->string('address_neighbour');
			$table->string('address_city');
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
			$table->dropColumn('status_id');
			$table->dropColumn('address_number');
			$table->dropColumn('address_complements');
			$table->dropColumn('address_neighbour');
			$table->dropColumn('address_city');
		});
	}

}
