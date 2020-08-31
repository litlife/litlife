<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('users')) {
			Schema::create('users', function (Blueprint $table) {
				$table->bigInteger('id', true);
				$table->integer('user_group_id')->default(0)->index();
				$table->integer('ec')->default(0);
				$table->string('email', 64);
				$table->string('nick')->nullable()->index('users_nick_trgm_idx');
				$table->integer('last_activity')->default(0);
				$table->string('last_name')->nullable()->default('')->index('users_last_name_trgm_idx');
				$table->string('first_name')->nullable()->default('')->index('users_first_name_trgm_idx');
				$table->string('middle_name')->nullable()->default('');
				$table->string('password', 32);
				$table->integer('gender')->default(0)->index();
				$table->integer('reg_date')->default(0);
				$table->smallInteger('new_message_count')->default(0);
				$table->string('reg_ip_old', 15)->nullable();
				$table->integer('my_books_count')->default(0);
				$table->smallInteger('version')->default(0);
				$table->integer('comment_count')->default(0)->index();
				$table->integer('user_lib_author_count')->default(0);
				$table->integer('user_lib_book_count')->default(0);
				$table->integer('user_lib_sequence_count')->default(0);
				$table->integer('forum_message_count')->default(0)->index();
				$table->date('born_date')->nullable()->index();
				$table->smallInteger('born_date_show')->default(0);
				$table->integer('book_rate_count')->default(0);
				$table->integer('book_read_count')->default(0);
				$table->integer('book_read_later_count')->default(0);
				$table->integer('book_read_now_count')->default(0);
				$table->string('city')->nullable();
				$table->smallInteger('name_show_type')->default(0);
				$table->integer('book_read_not_complete_count')->default(0);
				$table->smallInteger('hide')->default(0);
				$table->integer('hide_time')->default(0);
				$table->integer('hide_user')->default(0);
				$table->integer('book_file_count')->default(0);
				$table->integer('profile_comment_count')->default(0);
				$table->integer('subscriptions_count')->default(0);
				$table->integer('subscribers_count')->default(0);
				$table->integer('friends_count')->default(0);
				$table->integer('blacklists_count')->default(0);
				$table->smallInteger('hide_email')->default(1);
				$table->smallInteger('invite_send')->default(0);
				$table->integer('book_read_not_read_count')->default(0);
				$table->string('text_status')->nullable()->index();
				$table->bigInteger('avatar_id')->nullable()->index();
				$table->timestamps();
				$table->softDeletes();
				$table->dateTime('last_activity_at')->nullable()->index();
				$table->dateTime('suspended_at')->nullable();
				$table->smallInteger('photos_count')->default(0);
				$table->dateTime('user_edited_at')->nullable();
				$table->string('url_address', 32)->nullable();
				$table->smallInteger('topics_count')->default(0);
				$table->smallInteger('achievements_count')->default(0);
				$table->string('name_helper', 256)->nullable()->index()->comment('Вспомогательный столбец для быстрого trgm поиска');
				$table->string('reg_ip');
				$table->smallInteger('confirmed_mailbox_count')->default(0)->comment('Количество подтвержденных почтовых ящиков');
				$table->smallInteger('admin_notes_count')->default(0);
				$table->integer('miniature_image_id')->nullable();
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
		Schema::drop('users');
	}

}
