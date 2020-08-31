<?php

namespace App\Console\Commands\Old;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class OldTimeToNew extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:time_columns {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда переносит данные из старых столбцов времени в новые стандартные для ларавеля created_at updated_at и delete_at';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */

	/**
	 * Массив переделки данных
	 * название таблицы / имя нового столбца => имя старого столбца
	 */

	public function __construct()
	{
		parent::__construct();

		$this->array = [
			'action_logs' => [
				'created_at' => 'time'
			],
			'admin_notes' => [
				'created_at' => 'time'
			],
			'annotations' => [
				'user_edited_at' => 'edit_time'
			],

			'author_biographies' => [
				'user_edited_at' => 'edit_time'
			],
			'author_groups' => [
				'created_at' => 'time'
			],
			'author_photos' => [
				'created_at' => 'time'
			],
			'author_repeats' => [
				'created_at' => 'time'
			],

			'authors' => [
				'created_at' => 'time',
				'user_edited_at' => 'edit_time',
				'deleted_at' => 'hide_time'
			],
			'blogs' => [
				'created_at' => 'add_time',
				'user_edited_at' => 'edit_time',
				'deleted_at' => 'hide_time'
			],
			'book_authors' => [
				'created_at' => 'time'
			],
			'book_file_download_logs' => [
				'created_at' => 'time'
			],
			'book_files' => [
				'created_at' => 'add_time',
				'updated_at' => 'edit_time',
				'deleted_at' => 'hide_time'
			],

			'book_keyword_votes' => [
				'created_at' => 'time'
			],
			'book_keywords' => [
				'created_at' => 'time',
				'deleted_at' => 'hide_time'
			],
			'book_votes' => [
				'created_at' => 'time'
			],
			'book_read_remember_pages' => [
				'created_at' => 'time'
			],
			'book_similar_votes' => [
				'created_at' => 'time'
			],
			'book_statuses' => [
				'created_at' => 'time'
			],
			'book_translators' => [
				'created_at' => 'time'
			],
			'bookmark_folders' => [
				'created_at' => 'time'
			],
			'bookmarks' => [
				'created_at' => 'time'
			],
			'books' => [
				'created_at' => 'time_add',
				'user_edited_at' => 'time_edit',
				'deleted_at' => 'hide_time',
			],
			'comment_votes' => [
				'created_at' => 'time'
			],
			'comments' => [
				'created_at' => 'time',
				'user_edited_at' => 'edit_time',
				'deleted_at' => 'hide_time'
			],
			'email_changes' => [
				'created_at' => 'time'
			],
			'email_confirms' => [
				'created_at' => 'time'
			],
			'forums' => [
				'created_at' => 'create_time',
				'deleted_at' => 'hide_time'
			],
			'forum_groups' => [
				'created_at' => 'create_time'
			],
			'posts' => [
				'created_at' => 'create_time',
				'user_edited_at' => 'edit_time',
				'deleted_at' => 'hide_time'
			],
			'topics' => [
				'created_at' => 'create_time',

				'deleted_at' => 'hide_time'
			],
			'images' => [
				'created_at' => 'add_time'
			],

			'keywords' => [
				'deleted_at' => 'hide_time'
			],

			'likes' => [
				'created_at' => 'time'
			],
			'lost_passwords' => [
				'created_at' => 'time'
			],

			'messages' => [
				'created_at' => 'create_time',
			],

			'managers' => [
				'created_at' => 'add_time'
			],
			'sequences' => [
				'user_edited_at' => 'update_time',
				'deleted_at' => 'hide_time'
			],
			'variables' => [
				'created_at' => 'update_time',
				'updated_at' => 'update_time'
			],
			'text_blocks' => [
				'created_at' => 'time',
				'user_edited_at' => 'time'
			],
			'user_auth_fails' => [
				'created_at' => 'time',
				'updated_at' => 'time'
			],
			'user_auth_logs' => [
				'created_at' => 'time',
				'updated_at' => 'time'
			],
			'user_authors' => [
				'created_at' => 'time',
				'updated_at' => 'time'
			],
			'user_books' => [
				'created_at' => 'time',
				'updated_at' => 'time'
			],
			'user_sequences' => [
				'created_at' => 'time',
				'updated_at' => 'time'
			],
			'users' => [
				'created_at' => 'reg_date',
				'suspended_at' => 'hide_time',
				'last_activity_at' => 'last_activity',
			],
			'user_datas' => [
				'news_last_time_watch' => 'old_friends_news_last_time_watch',
			],
			'users_on_moderation' => [
				'created_at' => 'time',
				'updated_at' => 'time'
			],
			'user_relations' => [
				'created_at' => 'time',
				'updated_at' => 'time'
			]
		];
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$limit = $this->argument('limit');

		foreach ($this->array as $table => $columns) {
			$this->tableHandle($table, $columns);
		}


	}


	public function tableHandle($table, $columns)
	{
		foreach ($columns as $new => $old) {

			echo($table . ' ' . $new . ' ' . $old . "\n");

			DB::table($table)
				->where($old, '!=', 0)
				->update([
					$new => DB::raw('(CASE "' . $old . '" ' .
						'WHEN 0 THEN null ' .
						'WHEN null THEN null ' .
						'ELSE to_timestamp("' . $old . '") at time zone \'UTC\' END)')
				]);
		}
	}
}
