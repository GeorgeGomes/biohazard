<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDestinationVisibleAndColorService extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('provider_type', function(Blueprint $table)
		{
			$table->string('color')->nullable()->default("#333333");
			$table->boolean('destination_visible')->default(true);
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
			$table->dropColumn('color');
			$table->dropColumn('destination_visible');
		});
	}

}
