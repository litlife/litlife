<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessageDeletionsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('message_deletions')) {
			Schema::create('message_deletions', function (Blueprint $table) {
				$table->integer('message_id')->index();
				$table->integer('user_id')->index();
				$table->dateTime('deleted_at');
				$table->unique(['message_id', 'user_id']);
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
		Schema::drop('message_deletions');
	}

}
