<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserGroupsAddSendASupportQuestionColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_groups', function (Blueprint $table) {
			$table->boolean('send_a_support_question')->default(false)->comment(__('user_group.send_a_support_question'));
		});

		Schema::table('user_groups', function (Blueprint $table) {
			$table->boolean('reply_to_support_service')->default(false)->comment(__('user_group.reply_to_support_service'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_groups', function (Blueprint $table) {
			$table->dropColumn('send_a_support_question');
		});

		Schema::table('user_groups', function (Blueprint $table) {
			$table->dropColumn('reply_to_support_service');
		});
	}
}
