<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookVotesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_votes')) {
			Schema::create('book_votes', function (Blueprint $table) {
				$table->bigInteger('book_id')->index();
				$table->bigInteger('create_user_id')->index();
				$table->integer('rate')->default(0);
				$table->integer('time')->default(0);
				$table->smallInteger('hide')->default(0);
				$table->smallInteger('vote');
				$table->string('ip')->nullable();
				$table->timestamps();
				$table->softDeletes();
				$table->bigInteger('id', true);
				$table->dateTime('user_updated_at')->index();
				$table->unique(['book_id', 'create_user_id']);
				$table->unique(['book_id', 'create_user_id', 'vote']);
				$table->index(['create_user_id', 'user_updated_at'], 'book_votes_create_user_id_user_updated_at_asc_indexon');
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
		Schema::drop('book_votes');
	}

}
