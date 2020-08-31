<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersWalletsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_payment_details', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('user_id')->comment('ID пользователя которму принадлежат платежные данные');
			$table->string('type', 10)->comment('Тип платежной системы');
			$table->string('number', 20)->comment('Номер кошелька или карты');
			/*
			$table->string('card_number', 16)->nullable()->comment('Номер карты');
			$table->string('wmr', 13)->nullable()->comment('Номер кошелька в webmoney wmr');
			$table->string('yandex', 15)->nullable()->comment('Номер кошелька в яндекс деньги');
			$table->string('qiwi', 11)->nullable()->comment('Номер кошелька в qiwi');
			*/
			$table->timestamps();
			$table->softDeletes();

			$table->index(['user_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_payment_details');
	}
}
