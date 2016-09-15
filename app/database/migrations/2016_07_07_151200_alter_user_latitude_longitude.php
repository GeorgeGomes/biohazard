<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserLatitudeLongitude extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		DB::statement('ALTER TABLE `user` MODIFY `latitude` DOUBLE NULL DEFAULT 0 ;');
		DB::statement('ALTER TABLE `user` MODIFY `longitude` DOUBLE NULL DEFAULT 0 ;');
		DB::statement('ALTER TABLE `user` MODIFY `referred_by` INTEGER NULL  ;');
		DB::statement('ALTER TABLE `user` MODIFY `user_id` INTEGER NULL  ;');
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
