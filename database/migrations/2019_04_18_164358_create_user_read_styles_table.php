<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserReadStylesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_read_styles')) {
			Schema::create('user_read_styles', function (Blueprint $table) {
				$table->integer('user_id')->primary('user_read_styles_pkey');
				$table->smallInteger('font')->nullable();
				$table->smallInteger('align')->nullable();
				$table->smallInteger('size')->nullable();
				$table->string('background_color', 6)->nullable();
				$table->string('font_color', 6)->nullable();
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
		Schema::drop('user_read_styles');
	}

}
