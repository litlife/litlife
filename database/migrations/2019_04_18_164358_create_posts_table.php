<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePostsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('posts')) {
			Schema::create('posts', function (Blueprint $table) {
				$table->integer('id', true);
				$table->bigInteger('topic_id')->index();
				$table->text('html_text');
				$table->integer('create_time')->default(0);
				$table->bigInteger('create_user_id')->index();
				$table->integer('edit_time')->default(0);
				$table->bigInteger('edit_user_id')->nullable()->default(0);
				$table->smallInteger('hide')->default(0);
				$table->integer('hide_time')->default(0);
				$table->integer('hide_user')->default(0);
				$table->string('tree')->nullable()->index('posts_tree_gin_index');
				$table->smallInteger('children_count')->default(0);
				$table->string('complain_user_ids')->nullable();
				$table->smallInteger('checked')->default(0);
				$table->integer('like_count')->default(0);
				$table->string('ip')->nullable();
				$table->integer('_lft')->default(0);
				$table->integer('_rgt')->default(0);
				$table->integer('parent_id')->nullable();
				$table->timestamps();
				$table->softDeletes();
				$table->dateTime('accepted_at')->nullable();
				$table->integer('forum_id')->nullable()->index();
				$table->dateTime('user_edited_at')->nullable();
				$table->dateTime('sent_for_review_at')->nullable();
				$table->boolean('private')->default(0);
				$table->smallInteger('level')->default(0);
				$table->boolean('image_size_defined')->default(0)->index();
				$table->text('bb_text')->nullable();
				$table->boolean('external_images_downloaded')->default(0);
				$table->integer('user_agent_id')->nullable();
				$table->dateTime('rejected_at')->nullable();
				$table->smallInteger('status')->nullable();
				$table->dateTime('status_changed_at')->nullable();
				$table->integer('status_changed_user_id')->nullable();
				$table->index(['_lft', '_rgt', 'parent_id'], 'forum_posts__lft__rgt_parent_id_index');
				$table->unique(['created_at', 'id']);
				$table->index(['status', 'status_changed_at']);
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
		Schema::drop('posts');
	}

}
