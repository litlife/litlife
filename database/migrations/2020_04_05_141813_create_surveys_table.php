<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSurveysTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_surveys', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('create_user_id');
			$table->string('do_you_read_books_or_download_them')->nullable()->comment(__('survey.do_you_read_books_or_download_them'));
			$table->json('what_file_formats_do_you_download')->nullable()->comment(__('survey.what_file_formats_do_you_download'));
			$table->text('how_improve_download_book_files')->nullable()->comment(__('survey.how_improve_download_book_files'));
			$table->tinyInteger('how_do_you_rate_the_convenience_of_reading_books_online')->nullable()->comment(__('survey.how_do_you_rate_the_convenience_of_reading_books_online'));
			$table->text('how_to_improve_the_convenience_of_reading_books_online')->nullable()->comment(__('survey.how_to_improve_the_convenience_of_reading_books_online'));
			$table->tinyInteger('how_do_you_rate_the_convenience_and_functionality_of_search')->nullable()->comment(__('survey.how_do_you_rate_the_convenience_and_functionality_of_search'));
			$table->text('how_to_improve_the_convenience_of_searching_for_books')->nullable()->comment(__('survey.how_to_improve_the_convenience_of_searching_for_books'));
			$table->tinyInteger('how_do_you_rate_the_site_design')->nullable()->comment(__('survey.how_do_you_rate_the_site_design'));
			$table->text('how_to_improve_the_site_design')->nullable()->comment(__('survey.how_to_improve_the_site_design'));
			$table->tinyInteger('how_do_you_assess_the_work_of_the_site_administration')->nullable()->comment(__('survey.how_do_you_assess_the_work_of_the_site_administration'));
			$table->text('how_improve_the_site_administration')->nullable()->comment(__('survey.how_improve_the_site_administration'));
			$table->text('what_do_you_like_on_the_site')->nullable()->comment(__('survey.what_do_you_like_on_the_site'));
			$table->text('what_you_dont_like_about_the_site')->nullable()->comment(__('survey.what_you_dont_like_about_the_site'));
			$table->text('what_you_need_on_our_site')->nullable()->comment(__('survey.what_you_need_on_our_site'));
			$table->json('what_site_features_are_interesting_to_you')->nullable()->comment(__('survey.what_site_features_are_interesting_to_you'));
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('surveys');
	}
}
