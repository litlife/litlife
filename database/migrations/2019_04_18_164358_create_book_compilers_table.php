<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookCompilersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_compilers')) {
			Schema::create('book_compilers', function (Blueprint $table) {
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
		Schema::drop('book_compilers');
	}

}
