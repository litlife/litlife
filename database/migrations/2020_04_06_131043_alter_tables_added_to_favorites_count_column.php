<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablesAddedToFavoritesCountColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('authors', function (Blueprint $table) {
			$table->integer('added_to_favorites_count')->default(0)->comment(__('author.added_to_favorites_count'));
		});

		Schema::table('books', function (Blueprint $table) {
			$table->integer('added_to_favorites_count')->default(0)->comment(__('book.added_to_favorites_count'));
		});

		Schema::table('sequences', function (Blueprint $table) {
			$table->integer('added_to_favorites_count')->default(0)->comment(__('sequence.added_to_favorites_count'));
		});

		Schema::table('books', function (Blueprint $table) {
			$table->dropColumn('user_lib_count');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('authors', function (Blueprint $table) {
			$table->dropColumn('added_to_favorites_count');
		});

		Schema::table('books', function (Blueprint $table) {
			$table->dropColumn('added_to_favorites_count');
		});

		Schema::table('sequences', function (Blueprint $table) {
			$table->dropColumn('added_to_favorites_count');
		});

		Schema::table('books', function (Blueprint $table) {
			$table->integer('user_lib_count')->nullable();
		});
	}
}
