<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBooksAddTitleAuthorsHelper extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('books', function (Blueprint $table) {
			$table->text('title_authors_helper')->nullable();
		});

		\Illuminate\Support\Facades\DB::statement('CREATE INDEX books_title_authors_helper ON books USING GIN (to_tsvector(\'english\', "title_authors_helper"));');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('books', function (Blueprint $table) {
			$table->dropColumn('title_authors_helper');
		});

		\Illuminate\Support\Facades\DB::statement('drop index if exists books_title_authors_helper;');
	}
}
