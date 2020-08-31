<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateForumGroupsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('forum_groups')) {
			Schema::create('forum_groups', function (Blueprint $table) {
				$table->integer('id', true);
				$table->text('name');
				$table->integer('create_time')->default(0);
				$table->bigInteger('create_user_id');
				$table->text('forum_sort')->nullable();
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
		Schema::drop('forum_groups');
	}

}
