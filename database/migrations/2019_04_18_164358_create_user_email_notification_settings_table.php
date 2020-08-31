<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserEmailNotificationSettingsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_email_notification_settings')) {
			Schema::create('user_email_notification_settings', function (Blueprint $table) {
				$table->integer('user_id', true);
				$table->boolean('news')->default(1)->comment('Когда появляется новость от администрации');
				$table->boolean('private_message')->default(1)->comment('Когда приходит личное сообщение');
				$table->boolean('forum_reply')->default(1)->comment('Когда приходит ответ на сообщение на форуме');
				$table->boolean('wall_message')->default(1)->comment('Когда появляется новое сообщение на стене');
				$table->boolean('comment_reply')->default(1)->comment('Когда кто-то отвечает на комментарий');
				$table->boolean('wall_reply')->default(1)->comment('Когда кто-то отвечает на мое сообщение на стене');
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
		Schema::drop('user_email_notification_settings');
	}

}
