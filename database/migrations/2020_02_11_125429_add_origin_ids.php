<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOriginIds extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('book_statuses', function (Blueprint $table) {
			$table->integer('origin_book_id')->nullable()->index();
		});

		Schema::table('book_votes', function (Blueprint $table) {
			$table->integer('origin_book_id')->nullable()->index();
		});

		Schema::table('book_keywords', function (Blueprint $table) {
			$table->integer('origin_book_id')->nullable()->index();
		});

		Schema::table('books', function (Blueprint $table) {
			$table->integer('main_book_id')->nullable()->index();
		});

		Schema::table('books', function (Blueprint $table) {
			$table->integer('editions_count')->nullable()->index();
		});

		Schema::table('comments', function (Blueprint $table) {
			$table->integer('origin_commentable_id')->nullable()->index();
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('book_statuses', function (Blueprint $table) {
			$table->dropColumn('origin_book_id');
		});

		Schema::table('book_votes', function (Blueprint $table) {
			$table->dropColumn('origin_book_id');
		});

		Schema::table('book_keywords', function (Blueprint $table) {
			$table->dropColumn('origin_book_id');
		});

		Schema::table('books', function (Blueprint $table) {
			$table->dropColumn('main_book_id');
		});

		Schema::table('books', function (Blueprint $table) {
			$table->dropColumn('editions_count');
		});

		Schema::table('comments', function (Blueprint $table) {
			$table->dropColumn('origin_commentable_id');
		});
	}
}