<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserPhotosTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_photos')) {
			Schema::create('user_photos', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->bigInteger('user_id')->index();
				$table->text('name');
				$table->integer('size');
				$table->timestamps();
				$table->softDeletes();
				$table->text('parameters')->nullable();
				$table->string('storage', 30)->default('old');
				$table->string('dirname')->nullable();
				$table->string('md5', 32)->nullable();
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
		Schema::drop('user_photos');
	}

}
