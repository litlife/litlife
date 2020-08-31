<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferredUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('referred_users', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('referred_by_user_id')->default(config('referred_users.referred_by_user_id'));
			$table->integer('referred_user_id')->default(config('referred_users.referred_user_id'));
			$table->tinyInteger('comission_buy_book')->default(config('referred_users.comission_buy_book'));
			$table->tinyInteger('comission_sell_book')->default(config('referred_users.comission_sell_book'));
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
		Schema::dropIfExists('referred_users');
	}
}
