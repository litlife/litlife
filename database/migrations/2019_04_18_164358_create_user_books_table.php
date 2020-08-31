<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserBooksTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_books')) {
			Schema::create('user_books', function (Blueprint $table) {
				$table->bigInteger('user_id');
				$table->bigInteger('book_id');
				$table->integer('time')->default(0);
				$table->timestamps();
				$table->integer('id', true);
				$table->unique(['user_id', 'book_id']);
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
		Schema::drop('user_books');
	}

}
