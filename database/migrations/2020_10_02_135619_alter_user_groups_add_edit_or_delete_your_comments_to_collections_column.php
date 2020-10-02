<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserGroupsAddEditOrDeleteYourCommentsToCollectionsColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_groups', function (Blueprint $table) {
			$table->boolean('edit_or_delete_your_comments_to_collections')->default(true)->comment(__('user_group.edit_or_delete_your_comments_to_collections'));
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
			$table->dropColumn('edit_or_delete_your_comments_to_collections');
		});
	}
}
