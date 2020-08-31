<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookSimilarVotesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_similar_votes')) {
			Schema::create('book_similar_votes', function (Blueprint $table) {
				$table->bigInteger('book_similar_id')->nullable();
				$table->bigInteger('create_user_id');
				$table->smallInteger('vote');
				$table->integer('time')->default(0);
				$table->timestamps();
				$table->integer('book_id')->index();
				$table->integer('other_book_id');
				$table->bigInteger('id', true);
				$table->unique(['book_id', 'other_book_id', 'create_user_id'], 'book_similar_votes_book_id_other_book_id_user_id_unique');
				$table->index(['other_book_id', 'create_user_id']);
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
		Schema::drop('book_similar_votes');
	}

}
