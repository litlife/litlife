<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateParticipationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('participations')) {
			Schema::create('participations', function (Blueprint $table) {
				$table->integer('user_id');
				$table->integer('conversation_id')->index();
				$table->smallInteger('new_messages_count')->default(0);
				$table->integer('latest_seen_message_id')->nullable();
				$table->integer('latest_message_id')->nullable();
				$table->dateTime('created_at')->nullable()->index();
				$table->unique(['user_id', 'conversation_id']);
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
		Schema::drop('participations');
	}

}
