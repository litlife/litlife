<?php

namespace Database\Seeders;

use App\UserGroup;
use Illuminate\Database\Seeder;

class UserGroupSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$group = new UserGroup();
		$group->name = 'Администратор';
		$group->key = \App\Enums\UserGroupEnum::Administrator;
		foreach ($group->getPermissions() as $permission) {
			$group->$permission = true;
		}
		$group->save();

		$group = new UserGroup();
		$group->name = 'Пользователь';
		$group->key = \App\Enums\UserGroupEnum::User;
		$group->add_comment = true;
		$group->comment_self_edit_only_time = true;
		$group->add_book = true;
		$group->send_message = true;
		$group->delete_message = true;
		$group->author_repeat_report_add = true;
		$group->add_forum_topic = true;
		$group->add_forum_post = true;
		$group->forum_edit_self_post_only_time = true;
		$group->author_editor_request = true;
		$group->vote_for_book = true;
		$group->book_keyword_add = true;
		$group->book_keyword_vote = true;
		$group->complain = true;
		$group->save();

		$group = new UserGroup();
		$group->name = 'Забаненный';
		$group->key = \App\Enums\UserGroupEnum::Banned;
		$group->save();

		$group = new UserGroup();
		$group->name = 'Автор';
		$group->key = \App\Enums\UserGroupEnum::Author;
		$group->save();

		$group = new UserGroup();
		$group->name = 'Активный комментатор';
		$group->key = \App\Enums\UserGroupEnum::ActiveCommentator;
		$group->save();

		$group = new UserGroup();
		$group->name = 'Мастер комментария';
		$group->key = \App\Enums\UserGroupEnum::CommentMaster;
		$group->save();
	}
}
