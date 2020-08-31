<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommentVotesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('comment_votes')) {
			Schema::create('comment_votes', function (Blueprint $table) {
				$table->bigInteger('comment_id')->default(0)->index();
				$table->bigInteger('create_user_id')->default(0);
				$table->smallInteger('vote')->default(0);
				$table->integer('time')->default(0);
				$table->string('ip')->nullable();
				$table->timestamps();
				$table->integer('id', true);
				$table->index(['comment_id', 'create_user_id']);
				$table->unique(['comment_id', 'create_user_id', 'vote']);
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
		Schema::drop('comment_votes');
	}

}
