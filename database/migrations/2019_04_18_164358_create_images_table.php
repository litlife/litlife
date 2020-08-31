<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImagesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('images')) {
			Schema::create('images', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->string('type', 8);
				$table->integer('add_time')->default(0);
				$table->integer('create_user_id')->index();
				$table->timestamps();
				$table->softDeletes();
				$table->string('name');
				$table->integer('size');
				$table->string('md5', 32)->nullable();
				$table->string('storage', 30)->default('old');
				$table->string('dirname')->nullable();
				$table->string('sha256_hash', 64)->nullable();
				$table->string('phash', 16)->nullable();
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
		Schema::drop('images');
	}

}
