<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookStatusesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('book_statuses')) {
			Schema::create('book_statuses', function (Blueprint $table) {
				$table->bigInteger('book_id');
				$table->bigInteger('user_id');
				$table->smallInteger('code')->default(0);
				$table->integer('time')->default(0);
				$table->dateTime('user_updated_at')->nullable();
				$table->integer('id', true);
				$table->string('status', 30);
				$table->index(['user_updated_at', 'book_id']);
				$table->index(['user_id', 'status']);
				$table->index(['book_id', 'status']);
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
		Schema::drop('book_statuses');
	}

}
