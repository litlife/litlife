<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPaymentTransactionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_payment_transactions', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->double('sum', 6, 2)->comment('Списание или пополненение на балансе');
			$table->integer('user_id')->comment('Аккаунт пользователя');
			$table->smallInteger('type')->comment('Тип операции');
			$table->smallInteger('operable_type')->comment('ID morph таблицы');
			$table->smallInteger('operable_id')->comment('ID в таблице');
			$table->tinyInteger('status')->comment('Статус платежа');
			$table->timestamp('status_changed_at')->comment('Дата изменения статуса платежа');
			$table->double('balance_before', 6, 2)->nullable()->comment('Баланс до проведения операции');
			$table->json('params')->nullable()->comment('Дополнительные данные');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['user_id', 'type', 'status']);
			$table->index(['operable_type', 'operable_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_payment_transactions');
	}
}
