<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserAuthorsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_authors')) {
			Schema::create('user_authors', function (Blueprint $table) {
				$table->bigInteger('user_id');
				$table->bigInteger('author_id');
				$table->integer('time')->default(0);
				$table->timestamps();
				$table->integer('id', true);
				$table->unique(['user_id', 'author_id']);
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
		Schema::drop('user_authors');
	}

}
