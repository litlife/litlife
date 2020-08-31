<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAnnotationsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('annotations')) {
			Schema::create('annotations', function (Blueprint $table) {
				$table->integer('book_id', true);
				$table->text('text')->nullable();
				$table->integer('edit_user')->default(0);
				$table->integer('edit_time')->default(0);
				$table->timestamps();
				$table->dateTime('user_edited_at')->nullable();
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
		Schema::drop('annotations');
	}

}
