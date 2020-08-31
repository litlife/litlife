<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookGenresTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_genres')) {
			Schema::create('book_genres', function (Blueprint $table) {
				$table->bigInteger('book_id');
				$table->integer('genre_id')->default(0);
				$table->integer('order')->nullable();
				$table->unique(['book_id', 'genre_id']);
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
		Schema::drop('book_genres');
	}

}
