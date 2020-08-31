<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('notifications')) {
			Schema::create('notifications', function (Blueprint $table) {
				$table->uuid('id')->primary();
				$table->string('type');
				$table->string('notifiable_type', 30);
				$table->bigInteger('notifiable_id');
				$table->text('data');
				$table->dateTime('read_at')->nullable();
				$table->timestamps();
				$table->index(['notifiable_type', 'notifiable_id']);
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
		Schema::drop('notifications');
	}

}
