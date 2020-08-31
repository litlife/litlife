<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookKeywordsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_keywords')) {
			Schema::create('book_keywords', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->bigInteger('book_id')->default(0)->index();
				$table->bigInteger('keyword_id')->default(0)->index();
				$table->bigInteger('create_user_id')->default(0);
				$table->integer('time')->default(0);
				$table->smallInteger('rating')->default(0);
				$table->smallInteger('hide')->default(0);
				$table->integer('hide_time')->nullable();
				$table->integer('hide_user')->nullable();
				$table->timestamps();
				$table->softDeletes();
				$table->dateTime('accepted_at')->nullable();
				$table->dateTime('sent_for_review_at')->nullable();
				$table->dateTime('rejected_at')->nullable();
				$table->smallInteger('status')->nullable();
				$table->dateTime('status_changed_at')->nullable();
				$table->integer('status_changed_user_id')->nullable();
				$table->index(['status', 'status_changed_at', 'deleted_at']);
				$table->unique(['book_id', 'keyword_id', 'create_user_id'], 'book_keywords_book_id_keyword_id_create_user_id_uindex');
			});
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('book_keywords');
	}

}
