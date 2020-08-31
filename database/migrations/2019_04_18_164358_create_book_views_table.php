<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookViewsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_views')) {
			Schema::create('book_views', function (Blueprint $table) {
				$table->bigInteger('book_id')->default(0);
				$table->integer('user_id')->nullable()->default(0)->index('bv_user_id_idx');
				$table->integer('time')->default(0);
				$table->string('ip')->nullable();
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
		Schema::drop('book_views');
	}

}
