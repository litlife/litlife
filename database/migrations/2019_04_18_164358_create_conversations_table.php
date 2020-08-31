<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConversationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('conversations')) {
			Schema::create('conversations', function (Blueprint $table) {
				$table->integer('id', true);
				$table->integer('latest_message_id')->index();
				$table->integer('messages_count')->default(0);
				$table->smallInteger('participations_count')->default(0);
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
		Schema::drop('conversations');
	}

}
