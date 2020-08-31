<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthorStatusesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('author_statuses')) {
			Schema::create('author_statuses', function (Blueprint $table) {
				$table->bigInteger('author_id');
				$table->bigInteger('user_id');
				$table->smallInteger('code')->default(0);
				$table->integer('id', true);
				$table->dateTime('user_updated_at')->nullable()->comment('Время последнего изменения статуса пользователем');
				$table->string('status', 30);
				$table->index(['author_id', 'status']);
				$table->unique(['author_id', 'user_id']);
				$table->index(['user_id', 'status']);
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
		Schema::drop('author_statuses');
	}

}
