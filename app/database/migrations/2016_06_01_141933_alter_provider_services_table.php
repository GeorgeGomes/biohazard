<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProviderServicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('provider_services', function(Blueprint $table)
		{
			$table->float('base_distance');
			$table->float('base_time');
			$table->float('distance_unit');
			$table->float('time_unit');
			$table->float('base_price_provider');
			$table->float('base_price_user');
			$table->float('commission_rate');
			$table->boolean('is_visible');
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
			$table->dropColumn('base_distance');
			$table->dropColumn('base_time');
			$table->dropColumn('distance_unit');
			$table->dropColumn('time_unit');
			$table->dropColumn('base_price_provider');
			$table->dropColumn('base_price_user');
			$table->dropColumn('commission_rate');
		});
	}

}
