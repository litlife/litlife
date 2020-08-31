<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAwardsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('awards')) {
			Schema::create('awards', function (Blueprint $table) {
				$table->integer('id', true);
				$table->string('title', 100)->unique();
				$table->string('description', 100)->nullable();
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
		Schema::drop('awards');
	}

}
