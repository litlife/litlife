<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGenresTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('genres')) {
			Schema::create('genres', function (Blueprint $table) {
				$table->integer('id', true);
				$table->smallInteger('genre_group_id')->default(0)->index();
				$table->string('name', 50)->index('genres_name_trgm_idx');
				$table->string('fb_code', 50)->unique();
				$table->integer('book_count')->default(0);
				$table->smallInteger('age')->default(0);
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
		Schema::drop('genres');
	}

}
