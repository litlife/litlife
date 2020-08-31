<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserGroupsAddCreateTextProcessingBooks extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_groups', function (Blueprint $table) {
			$table->boolean('create_text_processing_books')->default(false)->comment(__('user_group.add_book'));
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
			$table->dropColumn('create_text_processing_books');
		});
	}
}
