<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookmarkFoldersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('bookmark_folders')) {
			Schema::create('bookmark_folders', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->integer('create_user_id')->default(0)->index();
				$table->string('title', 100);
				$table->integer('time')->default(0);
				$table->integer('bookmark_count')->default(0);
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
		Schema::drop('bookmark_folders');
	}

}
