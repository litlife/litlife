<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthorRepeatPivotsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('author_repeat_pivots')) {
			Schema::create('author_repeat_pivots', function (Blueprint $table) {
				$table->integer('author_id');
				$table->integer('author_repeat_id');
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
		Schema::drop('author_repeat_pivots');
	}

}
