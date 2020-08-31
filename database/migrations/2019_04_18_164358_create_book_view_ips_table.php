<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookViewIpsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_view_ips')) {
			Schema::create('book_view_ips', function (Blueprint $table) {
				$table->string('ip');
				$table->bigInteger('book_id')->default(0);
				$table->smallInteger('count')->default(0);
				$table->index(['book_id', 'ip']);
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
		Schema::drop('book_view_ips');
	}

}
