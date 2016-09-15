<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPersonTypeToProviderBankAccountTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('provider_bank_account', function($table){
            $table->enum('person_type', array('individual', 'company'))->default('individual');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('provider_bank_account', function($table)
		{
			$table->dropColumn('person_type');
		});
	}

}
