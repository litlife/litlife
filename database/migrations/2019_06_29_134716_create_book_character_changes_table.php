<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookCharacterChangesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('book_character_changes', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->integer('sum')->comment('Количество символов, которое прибавилось или убавилось. Может быть положительным или отрицательным');
			$table->integer('book_id')->comment('ID книги');
			$table->integer('section_id')->comment('ID главы');
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
		Schema::dropIfExists('book_character_changes');
	}
}
