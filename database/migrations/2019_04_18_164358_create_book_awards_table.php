<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookAwardsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_awards')) {
			Schema::create('book_awards', function (Blueprint $table) {
				$table->integer('id', true);
				$table->integer('book_id');
				$table->integer('award_id');
				$table->smallInteger('year')->nullable();
				$table->timestamps();
				$table->integer('create_user_id');
				$table->unique(['book_id', 'award_id']);
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
		Schema::drop('book_awards');
	}

}
