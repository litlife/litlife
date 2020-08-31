<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserAchievementsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_achievements')) {
			Schema::create('user_achievements', function (Blueprint $table) {
				$table->integer('id', true);
				$table->integer('user_id')->index();
				$table->integer('achievement_id');
				$table->timestamps();
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
		Schema::drop('user_achievements');
	}

}
