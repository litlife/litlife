<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSubscriptionsEventNotificationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_subscriptions_event_notifications', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('notifiable_user_id')->comment(__('user_subscriptions_event_notifications.notifiable_user_id'));
			$table->string('eventable_type', 10)->comment(__('user_subscriptions_event_notifications.eventable_type'));
			$table->integer('eventable_id')->comment(__('user_subscriptions_event_notifications.eventable_id'));
			$table->smallInteger('event_type')->comment(__('user_subscriptions_event_notifications.event_type'));
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
		Schema::dropIfExists('user_subscriptions_event_notifications');
	}
}
