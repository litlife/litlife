<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserSequencesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_sequences')) {
			Schema::create('user_sequences', function (Blueprint $table) {
				$table->bigInteger('user_id');
				$table->bigInteger('sequence_id');
				$table->integer('time')->default(0);
				$table->timestamps();
				$table->integer('id', true);
				$table->unique(['user_id', 'sequence_id']);
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
		Schema::drop('user_sequences');
	}

}
