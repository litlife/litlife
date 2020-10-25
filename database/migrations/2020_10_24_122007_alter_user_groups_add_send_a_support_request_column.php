<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserGroupsAddSendASupportRequestColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_groups', function (Blueprint $table) {
			$table->boolean('send_a_support_request')->default(true)->comment(__('user_group.send_a_support_request'));
		});

		Schema::table('user_groups', function (Blueprint $table) {
			$table->boolean('reply_to_support_service')->default(false)->comment(__('user_group.send_a_support_request'));
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
			$table->dropColumn('send_a_support_request');
		});

		Schema::table('user_groups', function (Blueprint $table) {
			$table->dropColumn('reply_to_support_service');
		});
	}
}
