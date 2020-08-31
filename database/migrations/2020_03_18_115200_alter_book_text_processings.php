<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBookTextProcessings extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('book_text_processings', function (Blueprint $table) {
			$table->boolean('remove_italics')->default(false)->comment(__('book_text_processing.remove_bold'));
			$table->boolean('remove_spaces_before_punctuations_marks')->default(false)->comment(__('book_text_processing.remove_extra_spaces'));
			$table->boolean('add_spaces_after_punctuations_marks')->default(false)->comment(__('book_text_processing.remove_extra_spaces'));
			$table->boolean('merge_paragraphs_if_there_is_no_dot_at_the_end')->default(false)->comment(__('book_text_processing.merge_paragraphs_if_there_is_no_dot_at_the_end'));
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
			$table->dropColumn('remove_italics');
			$table->dropColumn('remove_spaces_before_punctuations_marks');
			$table->dropColumn('add_spaces_after_punctuations_marks');
			$table->dropColumn('merge_paragraphs_if_there_is_no_dot_at_the_end');
		});
	}
}
