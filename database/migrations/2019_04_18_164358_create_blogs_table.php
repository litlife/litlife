<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBlogsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('blogs')) {
			Schema::create('blogs', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->integer('blog_user_id')->default(0)->index();
				$table->integer('create_user_id')->default(0);
				$table->integer('add_time')->default(0);
				$table->text('bb_text')->nullable();
				$table->text('text');
				$table->integer('edit_time')->default(0);
				$table->smallInteger('hide')->default(0);
				$table->integer('hide_time')->default(0);
				$table->integer('hide_user')->default(0);
				$table->string('tree')->nullable()->index('blogs_tree_gin_index');
				$table->smallInteger('children_count')->default(0);
				$table->integer('like_count')->default(0);
				$table->smallInteger('action')->default(0);
				$table->integer('_lft')->default(0);
				$table->integer('_rgt')->default(0);
				$table->integer('parent_id')->nullable();
				$table->timestamps();
				$table->softDeletes();
				$table->dateTime('user_edited_at')->nullable();
				$table->smallInteger('level')->default(0);
				$table->boolean('image_size_defined')->default(0)->index();
				$table->boolean('external_images_downloaded')->default(0);
				$table->boolean('display_on_home_page')->default(0);
				$table->integer('user_agent_id')->nullable();
				$table->index(['display_on_home_page', 'created_at']);
				$table->index(['_lft', '_rgt', 'parent_id']);
				$table->index(['blog_user_id', 'create_user_id', 'tree', 'created_at'], 'blogs_blog_user_id_create_user_id_tree_created_at_desc_index');
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
		Schema::drop('blogs');
	}

}
