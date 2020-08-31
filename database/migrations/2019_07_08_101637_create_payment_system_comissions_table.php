<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentSystemComissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_system_comissions', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('payment_aggregator');
			$table->string('payment_system_type');
			$table->integer('transaction_type');
			$table->float('comission');
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
		Schema::dropIfExists('payment_system_comissions');
	}
}
