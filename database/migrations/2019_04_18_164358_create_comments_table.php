<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommentsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('comments')) {
			Schema::create('comments', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->integer('commentable_id')->default(0);
				$table->integer('old_commentable_type')->default(0);
				$table->integer('create_user_id')->default(0)->index();
				$table->integer('time')->default(0);
				$table->text('text');
				$table->string('ip_old')->nullable();
				$table->integer('vote_up')->default(0);
				$table->integer('vote_down')->default(0);
				$table->integer('is_spam')->default(0);
				$table->binary('user_vote_for_spam')->nullable();
				$table->text('bb_text')->nullable();
				$table->integer('edit_user_id')->nullable()->default(0);
				$table->integer('edit_time')->default(0);
				$table->integer('reputation_count')->default(0);
				$table->smallInteger('hide')->default(0);
				$table->integer('hide_time')->default(0);
				$table->integer('hide_user')->default(0);
				$table->string('complain_user_ids')->nullable();
				$table->smallInteger('checked')->default(0);
				$table->smallInteger('vote')->default(0);
				$table->integer('action')->default(0);
				$table->string('tree')->nullable()->index('comments_tree_gin_index');
				$table->smallInteger('children_count')->default(0);
				$table->smallInteger('hide_from_top')->default(0);
				$table->integer('_lft')->default(0);
				$table->integer('_rgt')->default(0);
				$table->integer('parent_id')->nullable();
				$table->timestamps();
				$table->softDeletes();
				$table->dateTime('user_edited_at')->nullable();
				$table->dateTime('accepted_at')->nullable();
				$table->dateTime('sent_for_review_at')->nullable();
				$table->string('commentable_type', 30)->default('blog');
				$table->smallInteger('level')->default(0);
				$table->boolean('image_size_defined')->default(0)->index();
				$table->boolean('external_images_downloaded')->default(0);
				$table->string('ip');
				$table->integer('user_agent_id')->nullable();
				$table->dateTime('rejected_at')->nullable();
				$table->smallInteger('status')->nullable();
				$table->dateTime('status_changed_at')->nullable();
				$table->integer('status_changed_user_id')->nullable();
				$table->index(['status', 'status_changed_at']);
				$table->index(['commentable_id', 'commentable_type']);
				$table->index(['_lft', '_rgt', 'parent_id']);
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
		Schema::drop('comments');
	}

}
