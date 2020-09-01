<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameOldColumns extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('activity_log', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
			$this->dropColumnIfExists($table, 'text');
		});

		Schema::table('admin_notes', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::dropIfExists('anchors');
		Schema::dropIfExists('annotations');
		Schema::dropIfExists('audits');

		Schema::table('author_biographies', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'edit_time');
		});

		Schema::table('author_groups', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::table('author_statuses', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'code');
		});

		Schema::table('authors', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'old_rating');
			$this->dropColumnIfExists($table, 'time');
			$this->dropColumnIfExists($table, 'action');
			$this->dropColumnIfExists($table, 'translate_books_count');
			$this->dropColumnIfExists($table, 'hide');
			$this->dropColumnIfExists($table, 'old_gender');
			$this->dropColumnIfExists($table, 'edit_time');
			$this->dropColumnIfExists($table, 'hide_time');
			$this->dropColumnIfExists($table, 'delete_user_id');
			$this->dropColumnIfExists($table, 'hide_reason');
			$this->dropColumnIfExists($table, 'user_show');
			$this->dropColumnIfExists($table, 'old_vote_average');
			$this->dropColumnIfExists($table, 'accepted_at');
			$this->dropColumnIfExists($table, 'sent_for_review_at');
			$this->dropColumnIfExists($table, 'check_user_id');
			$this->dropColumnIfExists($table, 'rejected_at');
		});

		Schema::table('blogs', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'add_time');
			$this->dropColumnIfExists($table, 'edit_time');
			$this->dropColumnIfExists($table, 'hide');
			$this->dropColumnIfExists($table, 'hide_time');
			$this->dropColumnIfExists($table, 'hide_user');
			$this->dropColumnIfExists($table, 'action');
			$this->dropColumnIfExists($table, '_lft');
			$this->dropColumnIfExists($table, '_rgt');
			$this->dropColumnIfExists($table, 'image_size_defined');
		});

		Schema::table('book_authors', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::dropIfExists('book_compilers');
		Schema::dropIfExists('book_covers');
		Schema::dropIfExists('book_editors');

		Schema::table('book_file_download_logs', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::table('book_files', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'add_time');
			$this->dropColumnIfExists($table, 'hide');
			$this->dropColumnIfExists($table, 'hide_time');
			$this->dropColumnIfExists($table, 'hide_user');
			$this->dropColumnIfExists($table, 'version');
			$this->dropColumnIfExists($table, 'download_count_update_time');
			$this->dropColumnIfExists($table, 'edit_time');
			$this->dropColumnIfExists($table, 'edit_user');
			$this->dropColumnIfExists($table, 'name_change');
			$this->dropColumnIfExists($table, 'action');
			$this->dropColumnIfExists($table, 'error');
			$this->dropColumnIfExists($table, 'accepted_at');
			$this->dropColumnIfExists($table, 'sent_for_review_at');
			$this->dropColumnIfExists($table, 'rejected_at');
		});

		Schema::dropIfExists('book_illustrators');

		Schema::table('book_keyword_votes', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::table('book_keywords', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
			$this->dropColumnIfExists($table, 'hide');
			$this->dropColumnIfExists($table, 'hide_time');
			$this->dropColumnIfExists($table, 'hide_user');
			$this->dropColumnIfExists($table, 'accepted_at');
			$this->dropColumnIfExists($table, 'sent_for_review_at');
			$this->dropColumnIfExists($table, 'rejected_at');
		});

		Schema::table('book_read_remember_pages', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::table('book_similar_votes', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
			$this->dropColumnIfExists($table, 'book_similar_id');
		});

		Schema::dropIfExists('book_similars');
		Schema::dropIfExists('book_source_files');

		Schema::table('book_statuses', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
			$this->dropColumnIfExists($table, 'code');
		});

		Schema::dropIfExists('book_translators');
		Schema::dropIfExists('book_views');

		Schema::table('book_votes', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'rate');
			$this->dropColumnIfExists($table, 'time');
			$this->dropColumnIfExists($table, 'hide');
		});

		Schema::table('bookmark_folders', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::table('bookmarks', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'url_old');
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::table('books', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'genre');
			$this->dropColumnIfExists($table, 'author');
			$this->dropColumnIfExists($table, 'book_name');
			$this->dropColumnIfExists($table, 'nis');
			$this->dropColumnIfExists($table, 'old_rating');
			$this->dropColumnIfExists($table, 'time_add');
			$this->dropColumnIfExists($table, 'dca');
			$this->dropColumnIfExists($table, 'rca');
			$this->dropColumnIfExists($table, 'series');
			$this->dropColumnIfExists($table, 'translator');
			$this->dropColumnIfExists($table, 'action');
			$this->dropColumnIfExists($table, 'sum_of_votes');
			$this->dropColumnIfExists($table, 'time_edit');
			$this->dropColumnIfExists($table, 'verion');
			$this->dropColumnIfExists($table, 'moderator_info');
			$this->dropColumnIfExists($table, 'hide');
			$this->dropColumnIfExists($table, 'redirect_to_book');
			$this->dropColumnIfExists($table, 'vote_info');
			$this->dropColumnIfExists($table, 'edit_user_id');
			$this->dropColumnIfExists($table, 'edit_time');
			$this->dropColumnIfExists($table, 'hide_time');
			$this->dropColumnIfExists($table, 'hide_user');
			$this->dropColumnIfExists($table, 'hide_reason');
			$this->dropColumnIfExists($table, 'type');
			$this->dropColumnIfExists($table, 'old_vote_average');
			$this->dropColumnIfExists($table, 'user_show');
			$this->dropColumnIfExists($table, 'old_formats');
			$this->dropColumnIfExists($table, 'secret_hide');
			$this->dropColumnIfExists($table, 'last_versions_count');
			$this->dropColumnIfExists($table, 'secret_hide_user_id');
			$this->dropColumnIfExists($table, 'male_vote_percent');
			$this->dropColumnIfExists($table, 'hide_from_top');
			$this->dropColumnIfExists($table, 'litres_id');
			$this->dropColumnIfExists($table, 'litres_id_by_isbn');
			$this->dropColumnIfExists($table, 'coollib_id');
			$this->dropColumnIfExists($table, 'secret_hide_reason');
			$this->dropColumnIfExists($table, 'lang');
			$this->dropColumnIfExists($table, 'year');
			$this->dropColumnIfExists($table, 'section_count');
			$this->dropColumnIfExists($table, 'accepted_at');
			$this->dropColumnIfExists($table, 'check_user_id');
			$this->dropColumnIfExists($table, 'connected_at');
			$this->dropColumnIfExists($table, 'connect_user_id');
			$this->dropColumnIfExists($table, 'delete_user_id');
			$this->dropColumnIfExists($table, 'sent_for_review_at');
			$this->dropColumnIfExists($table, 'rejected_at');
		});

		Schema::dropIfExists('categories');

		Schema::table('comment_votes', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::table('comments', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'old_commentable_type');
			$this->dropColumnIfExists($table, 'time');
			$this->dropColumnIfExists($table, 'ip_old');
			$this->dropColumnIfExists($table, 'is_spam');
			$this->dropColumnIfExists($table, 'user_vote_for_spam');
			$this->dropColumnIfExists($table, 'edit_time');
			$this->dropColumnIfExists($table, 'reputation_count');
			$this->dropColumnIfExists($table, 'hide');
			$this->dropColumnIfExists($table, 'hide_time');
			$this->dropColumnIfExists($table, 'hide_user');
			$this->dropColumnIfExists($table, 'complain_user_ids');
			$this->dropColumnIfExists($table, 'checked');
			$this->dropColumnIfExists($table, 'action');
			$this->dropColumnIfExists($table, '_lft');
			$this->dropColumnIfExists($table, '_rgt');
			$this->dropColumnIfExists($table, 'accepted_at');
			$this->dropColumnIfExists($table, 'sent_for_review_at');
			$this->dropColumnIfExists($table, 'rejected_at');
		});

		Schema::table('complaints', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'accepted_at');
			$this->dropColumnIfExists($table, 'sent_for_review_at');
			$this->dropColumnIfExists($table, 'rejected_at');
		});

		Schema::dropIfExists('coollib_blocklists');
		Schema::dropIfExists('curse_words');
		Schema::dropIfExists('download_counts');
		Schema::dropIfExists('email_changes');
		Schema::dropIfExists('email_confirms');

		Schema::table('forum_groups', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'create_time');
		});

		Schema::table('forums', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'create_time');
			$this->dropColumnIfExists($table, 'hide');
			$this->dropColumnIfExists($table, 'hide_time');
			$this->dropColumnIfExists($table, 'hide_user');
		});

		Schema::table('genres', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'old_genre_group_id');
		});

		Schema::dropIfExists('image_sizes');

		Schema::table('images', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'add_time');
		});

		Schema::table('keywords', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'action');
			$this->dropColumnIfExists($table, 'hide');
			$this->dropColumnIfExists($table, 'hide_time');
			$this->dropColumnIfExists($table, 'hide_user');
			$this->dropColumnIfExists($table, 'accepted_at');
			$this->dropColumnIfExists($table, 'sent_for_review_at');
			$this->dropColumnIfExists($table, 'rejected_at');
		});

		Schema::table('likes', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::dropIfExists('litres_books');
		Schema::dropIfExists('lost_passwords');

		Schema::table('managers', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'add_time');
			$this->dropColumnIfExists($table, 'hide');
			$this->dropColumnIfExists($table, 'hide_time');
			$this->dropColumnIfExists($table, 'hide_user');
			$this->dropColumnIfExists($table, 'disable_editing_for_co_author');
			$this->dropColumnIfExists($table, 'accepted_at');
			$this->dropColumnIfExists($table, 'sent_for_review_at');
			$this->dropColumnIfExists($table, 'rejected_at');
		});

		Schema::table('messages', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'is_read');
			$this->dropColumnIfExists($table, 'recepient_del');
			$this->dropColumnIfExists($table, 'sender_del');
			$this->dropColumnIfExists($table, 'create_time');
			$this->dropColumnIfExists($table, 'is_spam');
			$this->dropColumnIfExists($table, 'image_size_defined');
		});

		Schema::dropIfExists('moderator_requests');
		Schema::dropIfExists('old_genres_groups');

		Schema::table('posts', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'create_time');
			$this->dropColumnIfExists($table, 'edit_time');
			$this->dropColumnIfExists($table, 'hide');
			$this->dropColumnIfExists($table, 'hide_time');
			$this->dropColumnIfExists($table, 'hide_user');
			$this->dropColumnIfExists($table, 'complain_user_ids');
			$this->dropColumnIfExists($table, 'checked');
			$this->dropColumnIfExists($table, '_lft');
			$this->dropColumnIfExists($table, '_rgt');
			$this->dropColumnIfExists($table, 'accepted_at');
			$this->dropColumnIfExists($table, 'sent_for_review_at');
			$this->dropColumnIfExists($table, 'image_size_defined');
			$this->dropColumnIfExists($table, 'rejected_at');
		});

		Schema::table('sections', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'content');
			$this->dropColumnIfExists($table, '_lft');
			$this->dropColumnIfExists($table, '_rgt');
			$this->dropColumnIfExists($table, 'html_tags_ids');
		});

		Schema::table('sequences', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'hide');
			$this->dropColumnIfExists($table, 'hide_time');
			$this->dropColumnIfExists($table, 'hide_user');
			$this->dropColumnIfExists($table, 'update_time');
			$this->dropColumnIfExists($table, 'hide_reason');
			$this->dropColumnIfExists($table, 'accepted_at');
			$this->dropColumnIfExists($table, 'sent_for_review_at');
			$this->dropColumnIfExists($table, 'check_user_id');
			$this->dropColumnIfExists($table, 'delete_user_id');
			$this->dropColumnIfExists($table, 'rejected_at');
		});

		Schema::table('text_blocks', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::table('topics', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'create_time');
			$this->dropColumnIfExists($table, 'hide');
			$this->dropColumnIfExists($table, 'hide_time');
			$this->dropColumnIfExists($table, 'hide_user');
			$this->dropColumnIfExists($table, 'first_post_on_top');
		});

		Schema::table('user_auth_fails', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::table('user_authors', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::table('user_books', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::table('user_datas', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'book_added_comment_count');
			$this->dropColumnIfExists($table, 'last_ip');
			$this->dropColumnIfExists($table, 'old_friends_news_last_time_watch');
			$this->dropColumnIfExists($table, 'time_edit_profile');
		});

		Schema::table('user_groups', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'permissions');
		});

		Schema::table('user_relations', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::table('user_sequences', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::table('user_settings', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'email_delivery');
			$this->dropColumnIfExists($table, 'user_access');
			$this->dropColumnIfExists($table, 'permissions_to_act');
		});

		Schema::dropIfExists('user_status');

		Schema::table('users', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'last_activity');
			$this->dropColumnIfExists($table, 'photo');
			$this->dropColumnIfExists($table, 'reg_date');
			$this->dropColumnIfExists($table, 'new_message_count');
			$this->dropColumnIfExists($table, 'reg_ip_old');
			$this->dropColumnIfExists($table, 'permission');
			$this->dropColumnIfExists($table, 'read_style');
			$this->dropColumnIfExists($table, 'mail_notif');
			$this->dropColumnIfExists($table, 'version');
			$this->dropColumnIfExists($table, 'hide');
			$this->dropColumnIfExists($table, 'hide_time');
			$this->dropColumnIfExists($table, 'hide_user');
			$this->dropColumnIfExists($table, 'book_file_count');
			$this->dropColumnIfExists($table, 'profile_comment_count');
			$this->dropColumnIfExists($table, 'hide_email');
			$this->dropColumnIfExists($table, 'invite_send');
		});

		Schema::dropIfExists('users_many_accounts');

		Schema::table('users_on_moderation', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'time');
		});

		Schema::table('variables', function (Blueprint $table) {
			$this->dropColumnIfExists($table, 'update_time');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

	public function dropColumnIfExists(Blueprint $table, $column)
	{
		if (Schema::hasColumn($table->getTable(), $column)) {
			//$table->dropColumn($column);
			if (mb_substr($column, 0, 4) != 'old_')
				$table->renameColumn($column, 'old_' . $column);
		}
	}
}
