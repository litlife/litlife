<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserGenreBlacklistTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_genre_blacklist')) {
			Schema::create('user_genre_blacklist', function (Blueprint $table) {
				$table->integer('user_id');
				$table->integer('genre_id');
				$table->timestamps();
				$table->unique(['user_id', 'genre_id']);
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
		Schema::drop('user_genre_blacklist');
	}

}
