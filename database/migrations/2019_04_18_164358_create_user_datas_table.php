<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserDatasTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_datas')) {
			Schema::create('user_datas', function (Blueprint $table) {
				$table->bigInteger('user_id')->default(0)->unique();
				$table->text('favorite_authors')->nullable()->default('');
				$table->text('favorite_genres')->nullable()->default('');
				$table->text('favorite_music')->nullable()->default('');
				$table->text('i_love')->nullable()->default('');
				$table->text('i_hate')->nullable()->default('');
				$table->text('about_self')->nullable()->default('');
				$table->text('favorite_quote')->nullable()->default('');
				$table->integer('book_added_comment_count')->nullable()->default(0);
				$table->integer('blog_record_comment_count')->nullable()->default(0);
				$table->string('last_ip')->nullable();
				$table->integer('old_friends_news_last_time_watch')->default(0);
				$table->integer('time_edit_profile')->nullable()->default(0);
				$table->softDeletes();
				$table->timestamps();
				$table->integer('password_reset_count')->default(0);
				$table->dateTime('last_time_password_is_reset')->nullable();
				$table->dateTime('last_news_view_at')->nullable();
				$table->integer('created_books_count')->default(0);
				$table->integer('created_authors_count')->default(0);
				$table->integer('created_sequences_count')->default(0);
				$table->timestamp('favorite_authors_books_latest_viewed_at')->nullable();
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
		Schema::drop('user_datas');
	}

}
