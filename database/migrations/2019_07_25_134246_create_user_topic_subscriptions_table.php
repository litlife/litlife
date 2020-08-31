<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTopicSubscriptionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_topic_subscriptions', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('topic_id');
			$table->integer('user_id');
			$table->timestamps();
			$table->unique(['user_id', 'topic_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('user_topic_subscriptions');
	}
}
