<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePushSubscriptionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('push_subscriptions')) {
			Schema::create('push_subscriptions', function (Blueprint $table) {
				$table->integer('id', true);
				$table->integer('user_id')->index();
				$table->string('endpoint', 500)->unique();
				$table->string('public_key')->nullable();
				$table->string('auth_token')->nullable();
				$table->timestamps();
			});
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('push_subscriptions');
	}

}
