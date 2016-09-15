<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLedgerTotalReferrals extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		DB::statement('ALTER TABLE `ledger` MODIFY `total_referrals` INTEGER NULL  ;');
		DB::statement('ALTER TABLE `ledger` MODIFY `amount_earned` DOUBLE NULL DEFAULT 0  ;');
		DB::statement('ALTER TABLE `ledger` MODIFY `amount_spent` DOUBLE NULL DEFAULT 0 ;');
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
