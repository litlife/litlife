<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserGroupsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasTable('user_groups')) {
			Schema::create('user_groups', function (Blueprint $table) {
				$table->text('name');
				$table->text('permissions')->nullable();
				$table->integer('id', true);
				$table->timestamps();
				$table->softDeletes();
				$table->boolean('not_show_ad')->default(0);
				$table->boolean('change_users_group')->default(0);
				$table->boolean('manage_users_groups')->default(0);
				$table->boolean('user_moderate')->default(0);
				$table->boolean('user_delete')->default(0);
				$table->boolean('user_suspend')->default(0);
				$table->boolean('add_comment')->default(0);
				$table->boolean('comment_self_edit_only_time')->default(0);
				$table->boolean('comment_edit_my')->default(0);
				$table->boolean('comment_edit_other_user')->default(0);
				$table->boolean('delete_my_comment')->default(0);
				$table->boolean('delete_other_user_comment')->default(0);
				$table->boolean('comment_view_who_likes_or_dislikes')->default(0);
				$table->boolean('add_book')->default(0);
				$table->boolean('add_book_without_check')->default(0);
				$table->boolean('edit_self_book')->default(0);
				$table->boolean('edit_other_user_book')->default(0);
				$table->boolean('author_edit')->default(0);
				$table->boolean('delete_hide_author')->default(0);
				$table->boolean('sequence_delete')->default(0);
				$table->boolean('sequence_edit')->default(0);
				$table->boolean('sequence_merge')->default(0);
				$table->boolean('send_message')->default(0);
				$table->boolean('delete_message')->default(0);
				$table->boolean('delete_self_book')->default(0);
				$table->boolean('delete_other_user_book')->default(0);
				$table->boolean('check_books')->default(0);
				$table->boolean('connect_books')->default(0);
				$table->boolean('author_repeat_report_add')->default(0);
				$table->boolean('author_repeat_report_delete')->default(0);
				$table->boolean('author_repeat_report_edit')->default(0);
				$table->boolean('merge_authors')->default(0);
				$table->boolean('forum_group_handle')->default(0);
				$table->boolean('add_forum_forum')->default(0);
				$table->boolean('forum_edit_forum')->default(0);
				$table->boolean('delete_forum_forum')->default(0);
				$table->boolean('forum_list_manipulate')->default(0);
				$table->boolean('add_forum_topic')->default(0);
				$table->boolean('delete_forum_self_topic')->default(0);
				$table->boolean('delete_forum_other_user_topic')->default(0);
				$table->boolean('edit_forum_self_topic')->default(0);
				$table->boolean('edit_forum_other_user_topic')->default(0);
				$table->boolean('manipulate_topic')->default(0);
				$table->boolean('add_forum_post')->default(0);
				$table->boolean('forum_edit_self_post_only_time')->default(0);
				$table->boolean('forum_edit_self_post')->default(0);
				$table->boolean('forum_edit_other_user_post')->default(0);
				$table->boolean('forum_delete_self_post')->default(0);
				$table->boolean('forum_delete_other_user_post')->default(0);
				$table->boolean('forum_topic_merge')->default(0);
				$table->boolean('forum_move_topic')->default(0);
				$table->boolean('forum_move_post')->default(0);
				$table->boolean('forum_post_manage')->default(0);
				$table->boolean('blog')->default(0);
				$table->boolean('blog_other_user')->default(0);
				$table->boolean('moderator_add_remove')->default(0);
				$table->boolean('author_editor_request')->default(0);
				$table->boolean('author_editor_check')->default(0);
				$table->boolean('vote_for_book')->default(0);
				$table->boolean('book_rate_other_user_remove')->default(0);
				$table->boolean('book_secret_hide_set')->default(0);
				$table->boolean('book_file_add')->default(0);
				$table->boolean('book_file_add_without_check')->default(0);
				$table->boolean('book_file_add_to_self_book_without_check')->default(0);
				$table->boolean('book_file_add_check')->default(0);
				$table->boolean('book_file_delete')->default(0);
				$table->boolean('book_file_edit')->default(0);
				$table->boolean('book_keyword_add')->default(0);
				$table->boolean('book_keyword_add_without_check')->default(0);
				$table->boolean('book_keyword_remove')->default(0);
				$table->boolean('book_keyword_edit')->default(0);
				$table->boolean('book_keyword_moderate')->default(0);
				$table->boolean('book_keyword_vote')->default(0);
				$table->boolean('book_fb2_file_convert_divide_on_page')->default(0);
				$table->boolean('comment_add_vote')->default(0);
				$table->boolean('book_similar_vote')->default(0);
				$table->boolean('genre_add')->default(0);
				$table->boolean('like_click')->default(0);
				$table->boolean('edit_profile')->default(0);
				$table->boolean('edit_other_profile')->default(0);
				$table->boolean('add_genre_to_blacklist')->default(0);
				$table->boolean('author_group_and_ungroup')->default(0);
				$table->boolean('book_comments_manage')->default(0);
				$table->boolean('text_block')->default(0);
				$table->boolean('admin_comment')->default(0);
				$table->boolean('complain')->default(0);
				$table->boolean('complain_check')->default(0);
				$table->boolean('check_post_comments')->default(0);
				$table->boolean('access_to_closed_books')->default(0);
				$table->boolean('admin_panel_access')->default(0);
				$table->boolean('retry_failed_book_parse')->default(0);
				$table->boolean('achievement')->default(0);
				$table->boolean('watch_activity_logs')->default(0);
				$table->boolean('display_technical_information')->default(0);
				$table->boolean('refresh_counters')->default(0);
				$table->boolean('awards')->default(0)->comment('Создавать, редактировать, удалять награды');
				$table->boolean('access_send_private_messages_avoid_privacy_and_blacklists')->default(0);
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
		Schema::drop('user_groups');
	}

}
