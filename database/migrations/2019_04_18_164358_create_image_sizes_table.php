<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateImageSizesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('image_sizes')) {
			Schema::create('image_sizes', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->bigInteger('img_id')->default(0);
				$table->text('name');
				$table->bigInteger('size')->default(0);
				$table->string('md5');
				$table->smallInteger('width')->default(0);
				$table->smallInteger('height')->default(0);
				$table->smallInteger('type')->default(0);
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
		Schema::drop('image_sizes');
	}

}
