<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookCoversTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_covers')) {
			Schema::create('book_covers', function (Blueprint $table) {
				$table->bigInteger('book_id')->default(0)->index('bc_book_id_idx');
				$table->string('name');
				$table->integer('size')->default(0);
				$table->integer('time')->default(0);
				$table->smallInteger('width')->default(0);
				$table->smallInteger('height')->default(0);
				$table->smallInteger('type')->default(1);
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
		Schema::drop('book_covers');
	}

}
