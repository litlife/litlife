<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGenresGroupsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('genres_groups')) {
			Schema::create('genres_groups', function (Blueprint $table) {
				$table->integer('id', true);
				$table->string('name', 50)->unique();
				$table->integer('book_count')->default(0);
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
		Schema::drop('genres_groups');
	}

}
