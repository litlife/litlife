<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBookTextProcessingsCreateTidyChapterNamesColumn extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('book_text_processings', function (Blueprint $table) {
			$table->boolean('tidy_chapter_names')->default(false)->comment(__('book_text_processing.tidy_chapter_names'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('book_text_processings', function (Blueprint $table) {
			$table->dropColumn('tidy_chapter_names');
		});
	}
}
