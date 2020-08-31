<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateForumsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('forums')) {
			Schema::create('forums', function (Blueprint $table) {
				$table->integer('id', true);
				$table->text('name');
				$table->text('description')->nullable();
				$table->integer('create_time')->default(0);
				$table->bigInteger('create_user_id')->nullable();
				$table->integer('topic_count')->default(0);
				$table->integer('post_count')->default(0);
				$table->bigInteger('last_topic_id')->nullable()->default(0);
				$table->bigInteger('last_post_id')->nullable()->default(0);
				$table->integer('forum_group_id')->nullable()->default(0)->index();
				$table->string('obj_type', 30)->nullable();
				$table->bigInteger('obj_id')->nullable()->default(0);
				$table->smallInteger('hide')->default(0);
				$table->integer('hide_time')->default(0);
				$table->integer('hide_user')->default(0);
				$table->smallInteger('min_message_count')->default(5);
				$table->boolean('private')->default(0)->index();
				$table->text('private_user_ids')->nullable();
				$table->timestamps();
				$table->softDeletes();
				$table->dateTime('user_edited_at')->nullable();
				$table->boolean('autofix_first_post_in_created_topics')->nullable()->default(0);
				$table->boolean('order_topics_based_on_fix_post_likes')->nullable()->default(0);
				$table->boolean('is_idea_forum')->default(0);
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
		Schema::drop('forums');
	}

}
