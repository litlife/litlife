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
			$this->renameColumnIfExists($table, 'time');
			$this->renameColumnIfExists($table, 'text');
		});

		Schema::table('admin_notes', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		$this->renameTableIfExists('anchors');
		$this->renameTableIfExists('annotations');
		$this->renameTableIfExists('audits');

		Schema::table('author_biographies', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'edit_time');
		});

		Schema::table('author_groups', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		Schema::table('author_statuses', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'code');
		});

		Schema::table('authors', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'old_rating');
			$this->renameColumnIfExists($table, 'time');
			$this->renameColumnIfExists($table, 'action');
			$this->renameColumnIfExists($table, 'translate_books_count');
			$this->renameColumnIfExists($table, 'hide');
			$this->renameColumnIfExists($table, 'old_gender');
			$this->renameColumnIfExists($table, 'edit_time');
			$this->renameColumnIfExists($table, 'hide_time');
			$this->renameColumnIfExists($table, 'delete_user_id');
			$this->renameColumnIfExists($table, 'hide_reason');
			$this->renameColumnIfExists($table, 'user_show');
			$this->renameColumnIfExists($table, 'old_vote_average');
			$this->renameColumnIfExists($table, 'accepted_at');
			$this->renameColumnIfExists($table, 'sent_for_review_at');
			$this->renameColumnIfExists($table, 'check_user_id');
			$this->renameColumnIfExists($table, 'rejected_at');
		});

		Schema::table('blogs', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'add_time');
			$this->renameColumnIfExists($table, 'edit_time');
			$this->renameColumnIfExists($table, 'hide');
			$this->renameColumnIfExists($table, 'hide_time');
			$this->renameColumnIfExists($table, 'hide_user');
			$this->renameColumnIfExists($table, 'action');
			$this->renameColumnIfExists($table, '_lft');
			$this->renameColumnIfExists($table, '_rgt');
			$this->renameColumnIfExists($table, 'image_size_defined');
		});

		Schema::table('book_authors', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		$this->renameTableIfExists('book_compilers');
		$this->renameTableIfExists('book_covers');
		$this->renameTableIfExists('book_editors');

		Schema::table('book_file_download_logs', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		Schema::table('book_files', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'add_time');
			$this->renameColumnIfExists($table, 'hide');
			$this->renameColumnIfExists($table, 'hide_time');
			$this->renameColumnIfExists($table, 'hide_user');
			$this->renameColumnIfExists($table, 'version');
			$this->renameColumnIfExists($table, 'download_count_update_time');
			$this->renameColumnIfExists($table, 'edit_time');
			$this->renameColumnIfExists($table, 'edit_user');
			$this->renameColumnIfExists($table, 'name_change');
			$this->renameColumnIfExists($table, 'action');
			$this->renameColumnIfExists($table, 'error');
			$this->renameColumnIfExists($table, 'accepted_at');
			$this->renameColumnIfExists($table, 'sent_for_review_at');
			$this->renameColumnIfExists($table, 'rejected_at');
		});

		$this->renameTableIfExists('book_illustrators');

		Schema::table('book_keyword_votes', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		Schema::table('book_keywords', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
			$this->renameColumnIfExists($table, 'hide');
			$this->renameColumnIfExists($table, 'hide_time');
			$this->renameColumnIfExists($table, 'hide_user');
			$this->renameColumnIfExists($table, 'accepted_at');
			$this->renameColumnIfExists($table, 'sent_for_review_at');
			$this->renameColumnIfExists($table, 'rejected_at');
		});

		Schema::table('book_read_remember_pages', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		Schema::table('book_similar_votes', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
			$this->renameColumnIfExists($table, 'book_similar_id');
		});

		$this->renameTableIfExists('book_similars');
		$this->renameTableIfExists('book_source_files');

		Schema::table('book_statuses', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
			$this->renameColumnIfExists($table, 'code');
		});

		$this->renameTableIfExists('book_translators');
		$this->renameTableIfExists('book_views');

		Schema::table('book_votes', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'rate');
			$this->renameColumnIfExists($table, 'time');
			$this->renameColumnIfExists($table, 'hide');
		});

		Schema::table('bookmark_folders', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		Schema::table('bookmarks', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'url_old');
			$this->renameColumnIfExists($table, 'time');
		});

		Schema::table('books', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'genre');
			$this->renameColumnIfExists($table, 'author');
			$this->renameColumnIfExists($table, 'book_name');
			$this->renameColumnIfExists($table, 'nis');
			$this->renameColumnIfExists($table, 'old_rating');
			$this->renameColumnIfExists($table, 'time_add');
			$this->renameColumnIfExists($table, 'dca');
			$this->renameColumnIfExists($table, 'rca');
			$this->renameColumnIfExists($table, 'series');
			$this->renameColumnIfExists($table, 'translator');
			$this->renameColumnIfExists($table, 'action');
			$this->renameColumnIfExists($table, 'sum_of_votes');
			$this->renameColumnIfExists($table, 'time_edit');
			$this->renameColumnIfExists($table, 'verion');
			$this->renameColumnIfExists($table, 'moderator_info');
			$this->renameColumnIfExists($table, 'hide');
			$this->renameColumnIfExists($table, 'redirect_to_book');
			$this->renameColumnIfExists($table, 'vote_info');
			$this->renameColumnIfExists($table, 'edit_user_id');
			$this->renameColumnIfExists($table, 'edit_time');
			$this->renameColumnIfExists($table, 'hide_time');
			$this->renameColumnIfExists($table, 'hide_user');
			$this->renameColumnIfExists($table, 'hide_reason');
			$this->renameColumnIfExists($table, 'type');
			$this->renameColumnIfExists($table, 'old_vote_average');
			$this->renameColumnIfExists($table, 'user_show');
			$this->renameColumnIfExists($table, 'old_formats');
			$this->renameColumnIfExists($table, 'secret_hide');
			$this->renameColumnIfExists($table, 'last_versions_count');
			$this->renameColumnIfExists($table, 'secret_hide_user_id');
			$this->renameColumnIfExists($table, 'male_vote_percent');
			$this->renameColumnIfExists($table, 'hide_from_top');
			$this->renameColumnIfExists($table, 'litres_id');
			$this->renameColumnIfExists($table, 'litres_id_by_isbn');
			$this->renameColumnIfExists($table, 'coollib_id');
			$this->renameColumnIfExists($table, 'secret_hide_reason');
			$this->renameColumnIfExists($table, 'lang');
			$this->renameColumnIfExists($table, 'year');
			$this->renameColumnIfExists($table, 'section_count');
			$this->renameColumnIfExists($table, 'accepted_at');
			$this->renameColumnIfExists($table, 'check_user_id');
			$this->renameColumnIfExists($table, 'connected_at');
			$this->renameColumnIfExists($table, 'connect_user_id');
			$this->renameColumnIfExists($table, 'delete_user_id');
			$this->renameColumnIfExists($table, 'sent_for_review_at');
			$this->renameColumnIfExists($table, 'rejected_at');
		});

		$this->renameTableIfExists('categories');

		Schema::table('comment_votes', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		Schema::table('comments', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'old_commentable_type');
			$this->renameColumnIfExists($table, 'time');
			$this->renameColumnIfExists($table, 'ip_old');
			$this->renameColumnIfExists($table, 'is_spam');
			$this->renameColumnIfExists($table, 'user_vote_for_spam');
			$this->renameColumnIfExists($table, 'edit_time');
			$this->renameColumnIfExists($table, 'reputation_count');
			$this->renameColumnIfExists($table, 'hide');
			$this->renameColumnIfExists($table, 'hide_time');
			$this->renameColumnIfExists($table, 'hide_user');
			$this->renameColumnIfExists($table, 'complain_user_ids');
			$this->renameColumnIfExists($table, 'checked');
			$this->renameColumnIfExists($table, 'action');
			$this->renameColumnIfExists($table, '_lft');
			$this->renameColumnIfExists($table, '_rgt');
			$this->renameColumnIfExists($table, 'accepted_at');
			$this->renameColumnIfExists($table, 'sent_for_review_at');
			$this->renameColumnIfExists($table, 'rejected_at');
		});

		Schema::table('complaints', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'accepted_at');
			$this->renameColumnIfExists($table, 'sent_for_review_at');
			$this->renameColumnIfExists($table, 'rejected_at');
		});

		$this->renameTableIfExists('coollib_blocklists');
		$this->renameTableIfExists('curse_words');
		$this->renameTableIfExists('download_counts');
		$this->renameTableIfExists('email_changes');
		$this->renameTableIfExists('email_confirms');

		Schema::table('forum_groups', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'create_time');
		});

		Schema::table('forums', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'create_time');
			$this->renameColumnIfExists($table, 'hide');
			$this->renameColumnIfExists($table, 'hide_time');
			$this->renameColumnIfExists($table, 'hide_user');
		});

		Schema::table('genres', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'old_genre_group_id');
		});

		$this->renameTableIfExists('image_sizes');

		Schema::table('images', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'add_time');
		});

		Schema::table('keywords', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'action');
			$this->renameColumnIfExists($table, 'hide');
			$this->renameColumnIfExists($table, 'hide_time');
			$this->renameColumnIfExists($table, 'hide_user');
			$this->renameColumnIfExists($table, 'accepted_at');
			$this->renameColumnIfExists($table, 'sent_for_review_at');
			$this->renameColumnIfExists($table, 'rejected_at');
		});

		Schema::table('likes', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		$this->renameTableIfExists('litres_books');
		$this->renameTableIfExists('lost_passwords');

		Schema::table('managers', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'add_time');
			$this->renameColumnIfExists($table, 'hide');
			$this->renameColumnIfExists($table, 'hide_time');
			$this->renameColumnIfExists($table, 'hide_user');
			$this->renameColumnIfExists($table, 'disable_editing_for_co_author');
			$this->renameColumnIfExists($table, 'accepted_at');
			$this->renameColumnIfExists($table, 'sent_for_review_at');
			$this->renameColumnIfExists($table, 'rejected_at');
		});

		Schema::table('messages', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'is_read');
			$this->renameColumnIfExists($table, 'recepient_del');
			$this->renameColumnIfExists($table, 'sender_del');
			$this->renameColumnIfExists($table, 'create_time');
			$this->renameColumnIfExists($table, 'is_spam');
			$this->renameColumnIfExists($table, 'image_size_defined');
		});

		$this->renameTableIfExists('moderator_requests');
		$this->renameTableIfExists('old_genres_groups');

		Schema::table('posts', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'create_time');
			$this->renameColumnIfExists($table, 'edit_time');
			$this->renameColumnIfExists($table, 'hide');
			$this->renameColumnIfExists($table, 'hide_time');
			$this->renameColumnIfExists($table, 'hide_user');
			$this->renameColumnIfExists($table, 'complain_user_ids');
			$this->renameColumnIfExists($table, 'checked');
			$this->renameColumnIfExists($table, '_lft');
			$this->renameColumnIfExists($table, '_rgt');
			$this->renameColumnIfExists($table, 'accepted_at');
			$this->renameColumnIfExists($table, 'sent_for_review_at');
			$this->renameColumnIfExists($table, 'image_size_defined');
			$this->renameColumnIfExists($table, 'rejected_at');
		});

		Schema::table('sections', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'content');
			$this->renameColumnIfExists($table, '_lft');
			$this->renameColumnIfExists($table, '_rgt');
			$this->renameColumnIfExists($table, 'html_tags_ids');
		});

		Schema::table('sequences', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'hide');
			$this->renameColumnIfExists($table, 'hide_time');
			$this->renameColumnIfExists($table, 'hide_user');
			$this->renameColumnIfExists($table, 'update_time');
			$this->renameColumnIfExists($table, 'hide_reason');
			$this->renameColumnIfExists($table, 'accepted_at');
			$this->renameColumnIfExists($table, 'sent_for_review_at');
			$this->renameColumnIfExists($table, 'check_user_id');
			$this->renameColumnIfExists($table, 'delete_user_id');
			$this->renameColumnIfExists($table, 'rejected_at');
		});

		Schema::table('text_blocks', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		Schema::table('topics', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'create_time');
			$this->renameColumnIfExists($table, 'hide');
			$this->renameColumnIfExists($table, 'hide_time');
			$this->renameColumnIfExists($table, 'hide_user');
			$this->renameColumnIfExists($table, 'first_post_on_top');
		});

		Schema::table('user_auth_fails', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		Schema::table('user_authors', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		Schema::table('user_books', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		Schema::table('user_datas', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'book_added_comment_count');
			$this->renameColumnIfExists($table, 'last_ip');
			$this->renameColumnIfExists($table, 'old_friends_news_last_time_watch');
			$this->renameColumnIfExists($table, 'time_edit_profile');
		});

		Schema::table('user_groups', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'permissions');
		});

		Schema::table('user_relations', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		Schema::table('user_sequences', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		Schema::table('user_settings', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'email_delivery');
			$this->renameColumnIfExists($table, 'user_access');
			$this->renameColumnIfExists($table, 'permissions_to_act');
		});

		$this->renameTableIfExists('user_status');

		Schema::table('users', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'last_activity');
			$this->renameColumnIfExists($table, 'photo');
			$this->renameColumnIfExists($table, 'reg_date');
			$this->renameColumnIfExists($table, 'new_message_count');
			$this->renameColumnIfExists($table, 'reg_ip_old');
			$this->renameColumnIfExists($table, 'permission');
			$this->renameColumnIfExists($table, 'read_style');
			$this->renameColumnIfExists($table, 'mail_notif');
			$this->renameColumnIfExists($table, 'version');
			$this->renameColumnIfExists($table, 'hide');
			$this->renameColumnIfExists($table, 'hide_time');
			$this->renameColumnIfExists($table, 'hide_user');
			$this->renameColumnIfExists($table, 'book_file_count');
			$this->renameColumnIfExists($table, 'profile_comment_count');
			$this->renameColumnIfExists($table, 'hide_email');
			$this->renameColumnIfExists($table, 'invite_send');
		});

		$this->renameTableIfExists('users_many_accounts');

		Schema::table('users_on_moderation', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'time');
		});

		Schema::table('variables', function (Blueprint $table) {
			$this->renameColumnIfExists($table, 'update_time');
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

	public function renameColumnIfExists(Blueprint $table, $column)
	{
		if (Schema::hasColumn($table->getTable(), $column)) {
			//$table->dropColumn($column);
			if (mb_substr($column, 0, 4) != 'old_')
				$table->renameColumn($column, 'old_' . $column);
		}
	}

	public function renameTableIfExists($table)
	{
		if (Schema::hasTable($table)) {
			Schema::rename($table, 'old_' . $table);
		}
	}
}
