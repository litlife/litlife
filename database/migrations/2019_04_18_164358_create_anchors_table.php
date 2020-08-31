<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAnchorsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('anchors')) {
			Schema::create('anchors', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->bigInteger('book_id');
				$table->integer('section_id');
				$table->text('name');
				$table->integer('link_to_section')->nullable();
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
		Schema::drop('anchors');
	}

}
