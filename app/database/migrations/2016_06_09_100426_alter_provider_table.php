<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProviderTableAddAttendanceHistory extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('provider', function(Blueprint $table)
		{
			$table->text('attendance_history');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('provider', function(Blueprint $table)
		{
			$table->dropColumn('attendance_history');
		});
	}

}
