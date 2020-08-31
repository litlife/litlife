<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AlterUserOutgoingPaymentTableAddUuidColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_outgoing_payments', function (Blueprint $table) {
			$table->uuid('uniqid')->nullable()->comment('Уникальный номер транзакции');
		});

		$array = \Illuminate\Support\Facades\DB::table('user_outgoing_payments')
			->whereNull('uniqid')
			->get();

		foreach ($array as $key => $value) {
			\Illuminate\Support\Facades\DB::table('user_outgoing_payments')
				->where('id', $value->id)
				->update(['uniqid' => Str::uuid()]);
		}

		\Illuminate\Support\Facades\DB::statement('alter table user_outgoing_payments alter column uniqid set not null;');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_outgoing_payments', function (Blueprint $table) {
			$table->dropColumn('uniqid');
		});
	}
}
