<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBooksAddForbidToChangeColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('books', function (Blueprint $table) {
			$table->boolean('forbid_to_change')->nullable()->comment(__('book.forbid_to_change'));
		});

		Schema::table('user_groups', function (Blueprint $table) {
			$table->boolean('enable_disable_changes_in_book')->default(false)->comment(__('user_group.enable_disable_changes_in_book'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('books', function (Blueprint $table) {
			$table->dropColumn('forbid_to_change');
		});

		Schema::table('user_groups', function (Blueprint $table) {
			$table->dropColumn('enable_disable_changes_in_book');
		});
	}
}
