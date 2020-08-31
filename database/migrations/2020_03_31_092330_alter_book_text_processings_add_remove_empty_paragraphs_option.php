<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBookTextProcessingsAddRemoveEmptyParagraphsOption extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('book_text_processings', function (Blueprint $table) {
			$table->boolean('remove_empty_paragraphs')->default(false)->comment(__('book_text_processing.remove_bold'));
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
			$table->dropColumn('remove_empty_paragraphs');
		});
	}
}
