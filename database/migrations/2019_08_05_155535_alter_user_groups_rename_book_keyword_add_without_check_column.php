<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUserGroupsRenameBookKeywordAddWithoutCheckColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_groups', function (Blueprint $table) {
			$table->renameColumn('book_keyword_add_without_check', 'book_keyword_add_new_with_check');
		});

		Schema::table('user_groups', function (Blueprint $table) {
			$table->boolean('book_keyword_add_new_with_check')->default(false)->comment('Добавлять новые ключевые слова с проверкой')->change();
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
			$table->renameColumn('book_keyword_add_new_with_check', 'book_keyword_add_without_check');
		});
	}
}
