<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookKeywordVotesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_keyword_votes')) {
			Schema::create('book_keyword_votes', function (Blueprint $table) {
				$table->bigInteger('book_keyword_id')->default(0);
				$table->bigInteger('create_user_id')->default(0);
				$table->smallInteger('vote')->default(0);
				$table->integer('time')->default(0);
				$table->timestamps();
				$table->integer('id', true);
				$table->unique(['create_user_id', 'book_keyword_id']);
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
		Schema::drop('book_keyword_votes');
	}

}
