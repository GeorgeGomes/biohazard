<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddChargeProviderReturnToProviderTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('provider_type', function(Blueprint $table)
		{
			$table->boolean('charge_provider_return')->default(false);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('provider_type', function($table)
		{
			$table->dropColumn('charge_provider_return');
		});
	}

}