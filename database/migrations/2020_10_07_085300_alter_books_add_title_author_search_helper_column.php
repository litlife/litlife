<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBooksAddTitleAuthorSearchHelperColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('books', function (Blueprint $table) {
			$table->text('title_author_search_helper')->nullable()->comment(__('book.title_helper'));
		});

		\Illuminate\Support\Facades\DB::statement('CREATE INDEX books_title_author_search_helper_fulltext_index ON books USING GIN (to_tsvector(\'english\', "title_author_search_helper"));');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('books', function (Blueprint $table) {
			$table->dropIndex('books_title_author_search_helper_fulltext_index');
			$table->dropColumn('title_author_search_helper');
		});
	}
}
