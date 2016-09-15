<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProviderBankAccountTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('provider_bank_account', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('provider_id')->references('id')->on('provider')->onDelete('cascade');;
            $table->string('bank_id')->references('id')->on('bank');
            $table->string('agency');
            $table->string('account');
            $table->string('account_digit');
            $table->string('holder');
            $table->string('document');
            $table->string('recipient_id');
            $table->timestamps();
        });
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
