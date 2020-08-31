<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTopicsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('topics')) {
			Schema::create('topics', function (Blueprint $table) {
				$table->integer('id', true);
				$table->boolean('closed')->default(0);
				$table->bigInteger('forum_id')->index();
				$table->text('name');
				$table->text('description')->nullable()->default('');
				$table->integer('create_time')->default(0);
				$table->bigInteger('create_user_id')->index();
				$table->integer('post_count')->default(0);
				$table->bigInteger('view_count')->default(0);
				$table->integer('last_post_id')->nullable()->default(0);
				$table->smallInteger('hide')->default(0);
				$table->integer('hide_time')->default(0);
				$table->integer('hide_user')->default(0);
				$table->boolean('post_desc')->default(0);
				$table->smallInteger('main_priority')->default(0);
				$table->boolean('first_post_on_top')->default(0);
				$table->bigInteger('top_post_id')->nullable()->default(0);
				$table->smallInteger('forum_priority')->default(0);
				$table->boolean('hide_from_main_page')->default(0);
				$table->timestamps();
				$table->softDeletes();
				$table->dateTime('user_edited_at')->nullable();
				$table->dateTime('last_post_created_at')->nullable();
				$table->boolean('archived')->default(0);
				$table->smallInteger('label')->nullable();
				$table->index(['forum_priority', 'last_post_created_at', 'archived']);
				$table->index(['main_priority', 'last_post_created_at']);
				$table->index(['main_priority', 'last_post_created_at'], 'topics_main_priority_desc_last_post_created_at_desc_nulls_last_');
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
		Schema::drop('topics');
	}

}
