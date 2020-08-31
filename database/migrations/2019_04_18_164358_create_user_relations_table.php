<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserRelationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_relations')) {
			Schema::create('user_relations', function (Blueprint $table) {
				$table->bigInteger('user_id');
				$table->bigInteger('user_id2');
				$table->smallInteger('status')->nullable();
				$table->integer('time')->nullable();
				$table->timestamps();
				$table->bigInteger('id', true);
				$table->dateTime('user_updated_at');
				$table->unique(['user_id', 'user_id2']);
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
		Schema::drop('user_relations');
	}

}
