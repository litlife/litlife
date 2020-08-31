<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookmarksTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('bookmarks')) {
			Schema::create('bookmarks', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->integer('create_user_id')->default(0);
				$table->text('url_old')->nullable();
				$table->string('title', 256);
				$table->integer('time')->default(0);
				$table->integer('folder_id')->nullable()->index();
				$table->timestamps();
				$table->softDeletes();
				$table->text('url');
				$table->boolean('new')->default(1);
				$table->index(['create_user_id', 'url']);
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
		Schema::drop('bookmarks');
	}

}
