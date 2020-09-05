<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RestoreOldColumns extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('books', function (Blueprint $table) {
			$table->renameColumn('old_connected_at', 'connected_at');
			$table->renameColumn('old_connect_user_id', 'connect_user_id');
			$table->renameColumn('old_male_vote_percent', 'male_vote_percent');
			$table->renameColumn('old_edit_user_id', 'edit_user_id');
			$table->renameColumn('old_delete_user_id', 'delete_user_id');
			$table->renameColumn('old_secret_hide_reason', 'secret_hide_reason');
		});

		Schema::table('sections', function (Blueprint $table) {
			$table->renameColumn('old__lft', '_lft');
			$table->renameColumn('old__rgt', '_rgt');
		});

		Schema::table('messages', function (Blueprint $table) {
			$table->renameColumn('old_is_read', 'is_read');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}
}
