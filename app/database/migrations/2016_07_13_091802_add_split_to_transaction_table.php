<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSplitToTransactionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('transaction', function(Blueprint $table) {
            $table->string('split_id');
            $table->enum('split_status', array('waiting_funds', 'paid'));
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('transaction', function($table){
			$table->dropColumn('split_id');
			$table->dropColumn('split_status');
		});
	}

}
