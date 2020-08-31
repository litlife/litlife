<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookSequencesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_sequences')) {
			Schema::create('book_sequences', function (Blueprint $table) {
				$table->bigInteger('book_id')->index();
				$table->bigInteger('sequence_id');
				$table->integer('number')->nullable();
				$table->smallInteger('order')->nullable();
				$table->unique(['book_id', 'sequence_id']);
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
		Schema::drop('book_sequences');
	}

}
