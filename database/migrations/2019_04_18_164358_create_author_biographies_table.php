<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthorBiographiesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('author_biographies')) {
			Schema::create('author_biographies', function (Blueprint $table) {
				$table->bigInteger('author_id')->nullable()->default(0)->index();
				$table->text('text');
				$table->integer('edit_user_id')->default(0);
				$table->integer('edit_time')->default(0);
				$table->timestamps();
				$table->softDeletes();
				$table->dateTime('user_edited_at')->nullable();
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
		Schema::drop('author_biographies');
	}

}
