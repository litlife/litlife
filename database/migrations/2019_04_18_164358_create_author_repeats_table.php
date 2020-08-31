<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthorRepeatsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('author_repeats')) {
			Schema::create('author_repeats', function (Blueprint $table) {
				$table->integer('id', true);
				$table->bigInteger('create_user_id')->index('arr_user_id_idx');
				$table->integer('time')->default(0);
				$table->text('comment')->nullable();
				$table->timestamps();
				$table->softDeletes();
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
		Schema::drop('author_repeats');
	}

}
