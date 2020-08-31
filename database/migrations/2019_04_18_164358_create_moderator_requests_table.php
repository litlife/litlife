<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModeratorRequestsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('moderator_requests')) {
			Schema::create('moderator_requests', function (Blueprint $table) {
				$table->integer('id', true);
				$table->integer('author_id');
				$table->integer('user_id');
				$table->string('type', 10);
				$table->text('text');
				$table->timestamps();
				$table->softDeletes();
				$table->time('checked_at');
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
		Schema::drop('moderator_requests');
	}

}
