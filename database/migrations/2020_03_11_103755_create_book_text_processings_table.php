<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookTextProcessingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('book_text_processings', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('book_id')->index()->comment(__('book_text_processing.book_id'));
			$table->integer('create_user_id')->comment(__('book_text_processing.create_user_id'));
			$table->boolean('remove_bold')->default(false)->comment(__('book_text_processing.remove_bold'));
			$table->boolean('remove_extra_spaces')->default(false)->comment(__('book_text_processing.remove_extra_spaces'));
			$table->boolean('split_into_chapters')->default(false)->comment(__('book_text_processing.split_into_chapters'));
			$table->boolean('convert_new_lines_to_paragraphs')->default(false)->comment(__('book_text_processing.split_into_chapters'));
			$table->boolean('add_a_space_after_the_first_hyphen_in_the_paragraph')->default(false)->comment(__('book_text_processing.split_into_chapters'));
			$table->timestamps();
			$table->timestamp('started_at')->nullable()->comment(__('book_text_processing.started_at'));
			$table->timestamp('completed_at')->nullable()->comment(__('book_text_processing.completed_at'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('book_text_processings');
	}
}
