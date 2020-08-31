<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVariablesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('variables')) {
			Schema::create('variables', function (Blueprint $table) {
				$table->string('name')->unique();
				$table->text('value')->nullable();
				$table->integer('update_time')->default(0);
				$table->timestamps();
				$table->integer('id', true);
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
		Schema::drop('variables');
	}

}
