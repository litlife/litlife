<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserPaymentDetailsAddParamsColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_payment_details', function (Blueprint $table) {
			$table->json('params')->nullable()->comment('Дополнительная информация о платежных данных');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_payment_details', function (Blueprint $table) {
			$table->dropColumn('params');
		});
	}
}
