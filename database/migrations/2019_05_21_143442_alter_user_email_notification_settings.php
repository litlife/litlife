<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserEmailNotificationSettings extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_email_notification_settings', function (Blueprint $table) {
			$table->boolean('db_forum_reply')->default(true);
			$table->boolean('db_wall_message')->default(true);
			$table->boolean('db_comment_reply')->default(true);
			$table->boolean('db_wall_reply')->default(true);
			$table->boolean('db_book_finish_parse')->default(true);
			$table->boolean('db_like')->default(true);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_email_notification_settings', function (Blueprint $table) {
			$table->dropColumn('db_forum_reply');
			$table->dropColumn('db_wall_message');
			$table->dropColumn('db_comment_reply');
			$table->dropColumn('db_wall_reply');
			$table->dropColumn('db_book_finish_parse');
			$table->dropColumn('db_like');
		});
	}
}
