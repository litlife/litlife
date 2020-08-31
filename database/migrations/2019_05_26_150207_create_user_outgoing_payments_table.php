<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserOutgoingPaymentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_outgoing_payments', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('user_id')->comment('Аккаунт пользователя c которого идет платеж');
			$table->ipAddress('ip')->comment('IP пользователя, который заказывает выплату');
			$table->string('purse', 30)->comment('Номер кошелька на который перечисляется выплата');
			$table->string('payment_type', 20)->comment('Тип платежной системы на которую перечисляется выплата');
			$table->integer('wallet_id')->comment('ID кошелька для выплаты');
			$table->string('payment_aggregator', 10)->nullable()->comment('Платежный агрегатор через который осуществляется выплата');
			$table->bigInteger('payment_aggregator_transaction_id')->nullable()->comment('ID транзакции платежного агрегатора, через который происходит выплата');
			$table->text('params')->nullable()->comment('Данные полученные от платежной системы');
			$table->tinyInteger('retry_failed_count')->nullable()->comment('Сколько попыток было отправить платеж');
			$table->timestamp('last_failed_retry_at')->nullable()->comment('Время последней попытки отправить платеж');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_outgoing_payments');
	}
}
