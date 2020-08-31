<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategoriesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('categories')) {
			Schema::create('categories', function (Blueprint $table) {
				$table->integer('id', true);
				$table->string('name');
				$table->integer('_lft')->default(0);
				$table->integer('_rgt')->default(0);
				$table->integer('parent_id')->nullable();
				$table->timestamps();
				$table->softDeletes();
				$table->index(['_lft', '_rgt', 'parent_id']);
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
		Schema::drop('categories');
	}

}
