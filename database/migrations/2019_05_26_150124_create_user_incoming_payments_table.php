<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserIncomingPaymentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_incoming_payments', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->string('payment_type', 20)->comment('Код платежной системы');
			$table->integer('user_id')->comment('Аккаунт пользователя на который зачисляется платеж');
			$table->ipAddress('ip')->comment('IP с которого осуществляется платеж');
			$table->string('currency', 5)->comment('Код валюты');
			$table->bigInteger('payment_id')->nullable()->comment('ID транзакции внутри платежного агрегатора');
			$table->string('payment_aggregator', 10)->comment('Название платежного агрегатора приема платежей');
			$table->text('params')->nullable()->comment('Все данные платежа');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['payment_id', 'payment_aggregator']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_incoming_payments');
	}
}
