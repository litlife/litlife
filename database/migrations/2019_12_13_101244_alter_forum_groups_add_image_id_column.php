<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterForumGroupsAddImageIdColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('forum_groups', function (Blueprint $table) {
			$table->integer('image_id')->nullable()->comment(__('forum_group.image_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('forum_groups', function (Blueprint $table) {
			$table->dropColumn('image_id');
		});
	}
}
