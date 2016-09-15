<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOptionsToScheduledRequestsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('scheduled_requests', function($table){
            $table->integer('category_id')->unsigned()->defalult(null);
            $table->foreign('category_id')->references('id')->on('provider_type_categories');
			$table->string('vehicle_brand');
			$table->string('vehicle_plate');
			$table->string('vehicle_observations');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('scheduled_requests', function($table){
			$table->dropForeign('scheduled_requests_category_id_foreign');
			$table->dropColumn('category_id');
			$table->dropColumn('vehicle_brand');
			$table->dropColumn('vehicle_plate');
			$table->dropColumn('vehicle_observations');
        });
	}

}
