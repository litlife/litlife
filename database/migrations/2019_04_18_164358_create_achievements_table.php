<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAchievementsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('achievements')) {
			Schema::create('achievements', function (Blueprint $table) {
				$table->integer('id', true);
				$table->string('title', 100);
				$table->string('description', 256);
				$table->integer('image_id');
				$table->timestamps();
				$table->softDeletes();
				$table->integer('create_user_id');
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
		Schema::drop('achievements');
	}

}
