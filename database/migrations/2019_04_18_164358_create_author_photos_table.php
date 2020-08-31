<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthorPhotosTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('author_photos')) {
			Schema::create('author_photos', function (Blueprint $table) {
				$table->bigInteger('author_id')->default(0)->index();
				$table->smallInteger('type')->default(0);
				$table->string('name');
				$table->integer('size')->default(0);
				$table->integer('time')->default(0);
				$table->smallInteger('width')->default(0);
				$table->smallInteger('height')->default(0);
				$table->timestamps();
				$table->softDeletes();
				$table->bigInteger('id', true);
				$table->string('storage', 30)->default('old');
				$table->string('dirname')->nullable();
				$table->integer('create_user_id')->nullable();
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
		Schema::drop('author_photos');
	}

}
