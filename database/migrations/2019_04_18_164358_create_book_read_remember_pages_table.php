<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookReadRememberPagesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_read_remember_pages')) {
			Schema::create('book_read_remember_pages', function (Blueprint $table) {
				$table->bigInteger('book_id')->default(0);
				$table->bigInteger('user_id')->default(0);
				$table->integer('time')->default(0);
				$table->smallInteger('page')->default(0);
				$table->dateTime('updated_at');
				$table->smallInteger('inner_section_id')->nullable();
				$table->unique(['book_id', 'user_id']);
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
		Schema::drop('book_read_remember_pages');
	}

}
