<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserEmailNotificationSettingsAddDbCommentVoteUpColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_email_notification_settings', function (Blueprint $table) {
			$table->boolean('db_comment_vote_up')->default(true);
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
			$table->dropColumn('db_comment_vote_up');
		});
	}
}
