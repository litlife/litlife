<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookIllustratorsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_illustrators')) {
			Schema::create('book_illustrators', function (Blueprint $table) {
				$table->integer('book_id')->comment('ID книги');
				$table->integer('author_id')->comment('ID автора');
				$table->timestamps();
				$table->smallInteger('order');
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
		Schema::drop('book_illustrators');
	}

}
