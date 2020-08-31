<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPurchasesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_purchases', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->bigInteger('buyer_user_id')->comment('Аккаунт пользователя, который оплачивает');
			$table->bigInteger('seller_user_id')->comment('Аккаунт пользователя, который получает выплату');
			$table->string('purchasable_type', 20)->comment('Тип объекта за который происходит оплата');
			$table->bigInteger('purchasable_id')->comment('ID объекта за который происходит оплата');
			$table->double('price', 6, 2)->comment('Цена по которой куплен объект');
			$table->tinyInteger('site_commission')->comment('Комиссия сайта');
			$table->timestamps();
			$table->softDeletes();

			$table->index('buyer_user_id');
			$table->index('seller_user_id');
			$table->index(['purchasable_type', 'purchasable_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_purchases');
	}
}
