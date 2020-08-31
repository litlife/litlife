<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImagesUsesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('images_uses')) {
			Schema::create('images_uses', function (Blueprint $table) {
				$table->integer('image_id');
				$table->integer('imageable_id');
				$table->string('imageable_type', 30);
				$table->unique(['image_id', 'imageable_id', 'imageable_type']);
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
		Schema::drop('images_uses');
	}

}
